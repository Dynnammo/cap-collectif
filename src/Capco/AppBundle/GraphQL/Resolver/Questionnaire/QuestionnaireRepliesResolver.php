<?php

namespace Capco\AppBundle\GraphQL\Resolver\Questionnaire;

use Capco\AppBundle\Elasticsearch\ElasticsearchPaginatedResult;
use Capco\AppBundle\Elasticsearch\ElasticsearchPaginator;
use Capco\AppBundle\Search\ReplySearch;
use Capco\UserBundle\Entity\User;
use Capco\AppBundle\Entity\Questionnaire;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;
use Psr\Log\LoggerInterface;

class QuestionnaireRepliesResolver implements ResolverInterface
{
    private ReplySearch $replySearch;
    private LoggerInterface $logger;

    public function __construct(ReplySearch $replySearch, LoggerInterface $logger)
    {
        $this->replySearch = $replySearch;
        $this->logger = $logger;
    }

    public function __invoke(
        Questionnaire $questionnaire,
        Arg $args,
        ?User $viewer = null
    ): ConnectionInterface {
        $includeUnpublished = false;
        $includeDraft = false;
        $filters = [
            'published' => !$includeUnpublished,
            'draft' => $includeDraft,
        ];

        if (
            $viewer &&
            $args->offsetExists('includeUnpublished') &&
            true === $args->offsetGet('includeUnpublished')
        ) {
            unset($filters['published']);
        }

        if (
            $viewer &&
            $args->offsetExists('includeDraft') &&
            true === $args->offsetGet('includeDraft')
        ) {
            unset($filters['draft']);
        }

        $paginator = new ElasticsearchPaginator(function (?string $cursor, int $limit) use (
            $questionnaire,
            $filters
        ) {
            if (!$questionnaire->getStep()) {
                return new ElasticsearchPaginatedResult([], [], 0);
            }

            try {
                return $this->replySearch->getRepliesByStep(
                    $questionnaire->getStep()->getId(),
                    $filters,
                    $limit,
                    $cursor
                );
            } catch (\RuntimeException $exception) {
                $this->logger->error(__METHOD__ . ' : ' . $exception->getMessage());

                throw new \RuntimeException('An error occured while retrieving replies.');
            }
        });

        return $paginator->auto($args);
    }
}
