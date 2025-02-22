<?php

namespace Capco\AppBundle\GraphQL\Resolver\Debate;

use Capco\AppBundle\Entity\Debate\Debate;
use Capco\AppBundle\GraphQL\Resolver\Traits\ResolverTrait;
use Capco\AppBundle\Repository\DebateVoteRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class DebateViewerUnpublishedVotesResolver implements ResolverInterface
{
    use ResolverTrait;
    private DebateVoteRepository $repository;

    public function __construct(DebateVoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Debate $debate, Argument $args, $viewer): ConnectionInterface
    {
        $user = $this->preventNullableViewer($viewer);

        $paginator = new Paginator(function (int $offset, int $limit) use ($debate, $user) {
            if (0 === $offset && 0 === $limit) {
                return [];
            }

            return $this->repository
                ->getUnpublishedByDebateAndUser($debate, $user, $limit, $offset)
                ->getIterator()
                ->getArrayCopy();
        });
        $totalCount = $this->repository->countUnpublishedByDebateAndUser($debate, $user);

        return $paginator->auto($args, $totalCount);
    }
}
