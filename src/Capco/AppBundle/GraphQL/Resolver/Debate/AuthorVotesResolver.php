<?php

namespace Capco\AppBundle\GraphQL\Resolver\Debate;

use Capco\UserBundle\Entity\User;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;
use Capco\AppBundle\Repository\DebateVoteRepository;
use Overblog\GraphQLBundle\Relay\Connection\ConnectionInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class AuthorVotesResolver implements ResolverInterface
{
    private DebateVoteRepository $repository;

    public function __construct(DebateVoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(User $author, ?Argument $args = null): ConnectionInterface
    {
        if (!$args) {
            $args = new Argument(['first' => 0]);
        }

        $paginator = new Paginator(function (int $offset, int $limit) use ($author) {
            if (0 === $offset && 0 === $limit) {
                return [];
            }

            return $this->repository
                ->getPublishedByAuthor($author, $limit, $offset)
                ->getIterator()
                ->getArrayCopy();
        });
        $totalCount = $this->repository->countPublishedByAuthor($author);

        return $paginator->auto($args, $totalCount);
    }
}
