<?php

namespace Capco\AppBundle\GraphQL\Resolver\Query;

use Psr\Log\LoggerInterface;
use Capco\MediaBundle\Repository\MediaRepository;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class MediaQueryResolver implements ResolverInterface
{
    private MediaRepository $mediaRepository;
    private LoggerInterface $logger;

    public function __construct(MediaRepository $mediaRepository, LoggerInterface $logger)
    {
        $this->mediaRepository = $mediaRepository;
        $this->logger = $logger;
    }

    public function __invoke(Arg $args): ConnectionInterface
    {
        $totalCount = $this->mediaRepository->countAllWithoutCategory($args->offsetGet('term'));
        $paginator = new Paginator(function (int $offset, int $limit) use ($args) {
            try {
                return $this->mediaRepository
                    ->getWithoutCategoryPaginated($offset, $limit, $args->offsetGet('term'))
                    ->getIterator()
                    ->getArrayCopy();
            } catch (\RuntimeException $exception) {
                $this->logger->error(__METHOD__ . ' : ' . $exception->getMessage());

                throw new \RuntimeException('No media found.');
            }
        });

        $connection = $paginator->auto($args, $totalCount);
        $connection->setTotalCount($totalCount);

        return $connection;
    }
}
