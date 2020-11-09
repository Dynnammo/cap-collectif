<?php

namespace Capco\AppBundle\GraphQL\Mutation\Debate;

use Psr\Log\LoggerInterface;
use Capco\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Capco\AppBundle\Entity\Debate\Debate;
use Doctrine\DBAL\Driver\DriverException;
use Overblog\GraphQLBundle\Error\UserError;
use Overblog\GraphQLBundle\Relay\Node\GlobalId;
use Capco\AppBundle\Repository\DebateVoteRepository;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class RemoveDebateVoteMutation implements MutationInterface
{
    public const UNKNOWN_DEBATE = 'UNKNOWN_DEBATE';
    public const CLOSED_DEBATE = 'CLOSED_DEBATE';
    public const NO_VOTE_FOUND = 'NO_VOTE_FOUND';

    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private GlobalIdResolver $globalIdResolver;
    private DebateVoteRepository $repository;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        GlobalIdResolver $globalIdResolver,
        DebateVoteRepository $repository
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->globalIdResolver = $globalIdResolver;
        $this->repository = $repository;
    }

    public function __invoke(Arg $input, User $viewer): array
    {
        $debateId = $input->offsetGet('debateId');
        $debate = $this->globalIdResolver->resolve($debateId, $viewer);

        if (!$debate || !($debate instanceof Debate)) {
            $this->logger->error('Unknown argument `debateId`.', ['id' => $debateId]);

            return $this->generateErrorPayload(self::UNKNOWN_DEBATE);
        }

        if (!$debate->viewerCanParticipate($viewer)) {
            $this->logger->error('The debate is not open.', ['id' => $debateId]);

            return $this->generateErrorPayload(self::CLOSED_DEBATE);
        }

        $previousVote = $this->repository->getOneByDebateAndUser($debate, $viewer);

        if (!$previousVote) {
            $this->logger->error('No vote found for `debateId` and viewer.', [
                'id' => $debateId,
                'viewer' => $viewer->getId(),
            ]);

            return $this->generateErrorPayload(self::NO_VOTE_FOUND);
        }

        $previousVoteId = $previousVote->getId();
        $this->em->remove($previousVote);

        try {
            $this->em->flush();
        } catch (DriverException $e) {
            $this->logger->error(
                __METHOD__ . ' => ' . $e->getErrorCode() . ' : ' . $e->getMessage()
            );

            throw new UserError('Internal error, please try again.');
        }

        return $this->generateSuccessFulPayload($debate, $previousVoteId);
    }

    private function generateSuccessFulPayload(Debate $debate, string $id): array
    {
        return [
            'deletedVoteId' => GlobalId::toGlobalId('DebateVote', $id),
            'debate' => $debate,
            'errorCode' => null,
        ];
    }

    private function generateErrorPayload(string $message): array
    {
        return ['deletedVoteId' => null, 'debate' => null, 'errorCode' => $message];
    }
}
