<?php

namespace Capco\AppBundle\Resolver;

use Capco\AppBundle\Entity\Questions\AbstractQuestion;
use Capco\AppBundle\Entity\ReplyAnonymous;
use Capco\AppBundle\Enum\MajorityVoteTypeEnum;
use Capco\AppBundle\Utils\Text;
use Liuggio\ExcelBundle\Factory;
use Capco\AppBundle\Entity\Reply;
use Doctrine\ORM\EntityManagerInterface;
use Capco\AppBundle\Entity\Questionnaire;
use Capco\AppBundle\Command\Utils\ExportUtils;
use Overblog\GraphQLBundle\Definition\Argument;
use Capco\AppBundle\Entity\Responses\MediaResponse;
use Capco\AppBundle\Entity\Responses\AbstractResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Capco\AppBundle\GraphQL\Resolver\Media\MediaUrlResolver;
use Capco\AppBundle\GraphQL\Resolver\Questionnaire\QuestionnaireExportResultsUrlResolver;

class ProjectDownloadResolver
{
    protected EntityManagerInterface $em;
    protected TranslatorInterface $translator;
    protected UrlArrayResolver $urlArrayResolver;
    protected MediaUrlResolver $urlResolver;
    protected Factory $phpexcel;
    protected array $headers;
    protected array $data;
    protected array $customFields;
    private array $projectAdminExcludedHeaders;
    private QuestionnaireExportResultsUrlResolver $exportUrlResolver;

    public function __construct(
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        UrlArrayResolver $urlArrayResolver,
        MediaUrlResolver $urlResolver,
        Factory $phpexcel,
        QuestionnaireExportResultsUrlResolver $exportUrlResolver
    ) {
        $this->em = $em;
        $this->translator = $translator;
        $this->urlArrayResolver = $urlArrayResolver;
        $this->urlResolver = $urlResolver;
        $this->phpexcel = $phpexcel;
        $this->headers = [];
        $this->customFields = [];
        $this->data = [];
        $this->exportUrlResolver = $exportUrlResolver;
        $this->projectAdminExcludedHeaders = ['author_email', 'phone'];
    }

    public function getQuestionnaireHeaders(
        Questionnaire $questionnaire,
        bool $projectAdmin = false
    ): array {
        $headers = [
            'id',
            'published',
            'published_at',
            'author',
            'author_id',
            'author_email',
            'phone',
            'created_at',
            'updated_at',
            'anonymous',
            'draft',
            'undraft_at',
            'account',
            'noAccount_email',
            'noAccount_email_isConfirmed',
            'internalComm',
        ];

        if ($projectAdmin) {
            $headers = array_diff($headers, $this->projectAdminExcludedHeaders);
        }
        foreach ($questionnaire->getRealQuestions() as $question) {
            $headers[] = ['label' => Text::unslug($question->getSlug()), 'raw' => true];
        }

        return $headers;
    }

    public function getContent(
        Questionnaire $questionnaire,
        ExportUtils $exportUtils
    ): \PHPExcel_Writer_IWriter {
        $this->headers = $this->getQuestionnaireHeaders($questionnaire);
        $data = $this->getQuestionnaireData($questionnaire);
        $title = $this->exportUrlResolver->getFileName($questionnaire);

        foreach ($data as &$d) {
            foreach ($d as $key => $value) {
                $d[$key] = $exportUtils->parseCellValue($this->formatText($value));
            }
        }

        return $this->getWriterFromData($data, $this->headers, $title);
    }

    // Add item in correct section
    public function addItemToData($item): void
    {
        $this->data[] = $item;
    }

    public function getQuestionnaireData(
        Questionnaire $questionnaire,
        bool $projectAdmin = false
    ): array {
        $this->data = [];
        $userReplies = $this->em
            ->getRepository(Reply::class)
            ->getEnabledByQuestionnaireAsArray($questionnaire);

        $anonymousReplies = $this->em
            ->getRepository(ReplyAnonymous::class)
            ->getEnabledByQuestionnaireAsArray($questionnaire);

        $replies = array_merge($userReplies, $anonymousReplies);

        $this->getRepliesData($replies, $projectAdmin);

        foreach ($this->data as &$answers) {
            foreach ($answers as $key => $value) {
                $answers[$key] = $this->formatText($value);
            }
        }

        return $this->data;
    }

    public function getRepliesData(iterable $replies, bool $projectAdmin = false): void
    {
        foreach ($replies as $reply) {
            $responses = $this->em
                ->getRepository(AbstractResponse::class)
                ->getByReplyAsArray($reply['id']);
            $this->addItemToData($this->getReplyItem($reply, $responses, $projectAdmin));
        }
    }

    public function formatText($text): string
    {
        $oneBreak = ['<br>', '<br/>', '&nbsp;'];
        $twoBreaks = ['</p>'];
        $text = str_ireplace($oneBreak, "\r", $text);
        $text = str_ireplace($twoBreaks, "\r\n", $text);
        $text = strip_tags($text);

        return html_entity_decode($text, \ENT_QUOTES);
    }

    // *************************** Generate items *******************************************

    private function getReplyItem(array $reply, array $responses, bool $projectAdmin = false): array
    {
        $isAnonymousReply = !isset($reply['author']);
        $participantEmail = $isAnonymousReply ? $reply['participantEmail'] : '';
        $participantEmailIsConfirmed = $isAnonymousReply ? ($reply['emailConfirmed'] ? 'Yes' : 'No') : null;

        $item = [
            'id' => $reply['id'],
            'published' => $this->booleanToString($reply['published']),
            'published_at' => $this->dateToString($reply['publishedAt']),
            'author' => $isAnonymousReply ? '' : $reply['author']['username'],
            'author_id' => $isAnonymousReply ? '' : $reply['author']['id'],
            'author_email' => $isAnonymousReply ? '' : $reply['author']['email'],
            'phone' => (!$isAnonymousReply && $reply['author']['phone']) ? (string) $reply['author']['phone'] : '',
            'created_at' => $this->dateToString($reply['createdAt']),
            'updated_at' => $this->dateToString($reply['updatedAt']),
            'anonymous' => $isAnonymousReply ? '' : $this->booleanToString($reply['private']),
            'draft' => $isAnonymousReply ? '' : $this->booleanToString($reply['draft']),
            'undraft_at' => $isAnonymousReply ? '' : $this->dateToString($reply['undraftAt']),
            'account' => $this->booleanToString(!$isAnonymousReply),
            'noAccount_email' => $participantEmail,
            'noAccount_email_isConfirmed' => $participantEmailIsConfirmed,
            'internalComm' => $this->booleanToString(!empty($participantEmail)),
        ];

        if ($projectAdmin) {
            foreach ($this->projectAdminExcludedHeaders as $excludedHeader) {
                unset($item[$excludedHeader]);
            }
        }

        foreach ($responses as $response) {
            $question = $response['question'];
            $item[Text::unslug($question['slug'])] = $this->getResponseValue($response);
        }

        foreach ($this->headers as $header) {
            if (\is_array($header) && !isset($item[$header['label']])) {
                $item[$header['label']] = '';
            }
        }

        return $item;
    }

    private function getResponseValue(array $response)
    {
        $responseMedia = null;
        $mediasUrl = [];
        if ('media' === $response['response_type']) {
            $responseMedia = $this->em->getRepository(MediaResponse::class)->findOneBy([
                'id' => $response['id'],
            ]);

            foreach ($responseMedia->getMedias() as $media) {
                $mediasUrl[] = $this->urlResolver->__invoke(
                    $media,
                    new Argument(['format' => 'reference'])
                );
            }
        }

        $originalValue = $responseMedia ? implode(' ; ', $mediasUrl) : $response['value'];
        if (\is_array($originalValue)) {
            $values = $originalValue['labels'];
            if (isset($originalValue['other'])) {
                $values[] = $originalValue['other'];
            }

            return implode(';', $values);
        }

        if (
            AbstractQuestion::QUESTION_TYPE_MAJORITY_DECISION ===
                (int) $response['question']['type'] &&
            null !== $response['value']
        ) {
            return $this->translator->trans(
                MajorityVoteTypeEnum::toI18nKey($response['value']),
                [],
                'CapcoAppBundle'
            );
        }

        return $originalValue;
    }

    private function getWriterFromData($data, $headers, $title): \PHPExcel_Writer_IWriter
    {
        $phpExcelObject = $this->phpexcel->createPHPExcelObject();
        $phpExcelObject->getProperties()->setTitle($title);
        $phpExcelObject->setActiveSheetIndex();
        $sheet = $phpExcelObject->getActiveSheet();
        $sheet->setTitle($this->translator->trans('global.contribution', [], 'CapcoAppBundle'));
        \PHPExcel_Settings::setCacheStorageMethod(
            \PHPExcel_CachedObjectStorageFactory::cache_in_memory,
            ['memoryCacheSize' => '512M']
        );
        $nbCols = \count($headers);
        // Add headers
        list($startColumn, $startRow) = \PHPExcel_Cell::coordinateFromString();
        $currentColumn = $startColumn;
        foreach ($headers as $header) {
            if (\is_array($header)) {
                $header = $header['label'];
            } elseif (!\in_array($header, $this->customFields, true)) {
                $header = $this->translator->trans(
                    'project_download.label.' . $header,
                    [],
                    'CapcoAppBundle'
                );
            }
            $sheet->setCellValueExplicit($currentColumn . $startRow, $header);
            ++$currentColumn;
        }
        list($startColumn, $startRow) = \PHPExcel_Cell::coordinateFromString('A2');
        $currentRow = $startRow;
        // Loop through data
        foreach ($data as $row) {
            $currentColumn = $startColumn;
            for ($i = 0; $i < $nbCols; ++$i) {
                $headerKey = \is_array($headers[$i]) ? $headers[$i]['label'] : $headers[$i];
                $sheet->setCellValue($currentColumn . $currentRow, $row[$headerKey]);
                ++$currentColumn;
            }
            ++$currentRow;
        }

        // create the writer
        return $this->phpexcel->createWriter($phpExcelObject, 'Excel2007');
    }

    private function booleanToString($boolean): string
    {
        if ($boolean) {
            return 'Yes';
        }

        return 'No';
    }

    private function dateToString(?\DateTime $date = null): string
    {
        if ($date) {
            return $date->format('Y-m-d H:i:s');
        }

        return '';
    }
}
