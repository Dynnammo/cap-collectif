<?php

namespace Capco\AppBundle\GraphQL\Resolver\Question;

use Psr\Log\LoggerInterface;
use Capco\AppBundle\Entity\Questions\MediaQuestion;
use Capco\AppBundle\Entity\Questions\SimpleQuestion;
use Capco\AppBundle\Entity\Questions\SectionQuestion;
use Capco\AppBundle\Entity\Questions\AbstractQuestion;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Capco\AppBundle\Repository\MediaResponseRepository;
use Capco\AppBundle\Repository\ValueResponseRepository;
use Capco\AppBundle\Entity\Questions\MultipleChoiceQuestion;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Capco\AppBundle\GraphQL\ConnectionBuilder;

class QuestionResponsesResolver implements ResolverInterface
{
    private $mediaResponseRepository;
    private $valueResponseRepository;
    private $logger;

    public function __construct(
        ValueResponseRepository $valueResponseRepository,
        MediaResponseRepository $mediaResponseRepository,
        LoggerInterface $logger
    ) {
        $this->mediaResponseRepository = $mediaResponseRepository;
        $this->valueResponseRepository = $valueResponseRepository;
        $this->logger = $logger;
    }

    public function __invoke(AbstractQuestion $question, Arg $args, $viewer)
    {
        $emptyConnection = ConnectionBuilder::empty();

        if (
            $question->getQuestionnaire() &&
            $question->getQuestionnaire()->isPrivateResult() &&
            (!$viewer || !$viewer->isAdmin())
        ) {
            return $emptyConnection;
        }

        if ($question->getQuestionnaire() && !$question->getQuestionnaire()->canDisplay($viewer)) {
            return $emptyConnection;
        }

        $totalCount = 0;
        $arguments = $args->getArrayCopy();
        $withNotConfirmedUser =
            isset($arguments['withNotConfirmedUser']) &&
            true === $arguments['withNotConfirmedUser'];

        // Schema design is wrong but let's return empty connection for now…
        if ($question instanceof SectionQuestion) {
            return $emptyConnection;
        }

        if ($question instanceof MultipleChoiceQuestion || $question instanceof SimpleQuestion) {
            $totalCount = $this->valueResponseRepository->countByQuestion(
                $question,
                $withNotConfirmedUser
            );
        }
        if ($question instanceof MediaQuestion) {
            $totalCount = $this->mediaResponseRepository->countByQuestion(
                $question,
                $withNotConfirmedUser
            );
        }

        // get data of $question instanceof MultipleChoiceQuestion && $question instanceof SimpleQuestion && $question instanceof MediaQuestion
        $paginator = new Paginator(function ($offset, $limit) use (
            $question,
            $withNotConfirmedUser
        ) {
            try {
                if (
                    $question instanceof MultipleChoiceQuestion ||
                    $question instanceof SimpleQuestion
                ) {
                    $responses = $this->valueResponseRepository->getAllByQuestion(
                        $question,
                        $limit,
                        $offset,
                        $withNotConfirmedUser
                    );
                } else {
                    $responses = $this->mediaResponseRepository->getAllByQuestion(
                        $question,
                        $limit,
                        $offset,
                        $withNotConfirmedUser
                    );
                }

                return $responses->getIterator()->getArrayCopy();
            } catch (\RuntimeException $exception) {
                $this->logger->error(__METHOD__ . ' : ' . $exception->getMessage());

                throw new \RuntimeException('Find responses of survey failed');
            }
        });

        return $paginator->auto($args, $totalCount);
    }
}
