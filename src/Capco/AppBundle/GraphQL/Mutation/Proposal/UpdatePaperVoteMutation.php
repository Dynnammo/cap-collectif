<?php

namespace Capco\AppBundle\GraphQL\Mutation\Proposal;

use Capco\AppBundle\Entity\Proposal;
use Capco\AppBundle\Entity\ProposalStepPaperVoteCounter;
use Capco\AppBundle\Entity\Steps\AbstractStep;
use Capco\AppBundle\Entity\Steps\CollectStep;
use Capco\AppBundle\Entity\Steps\SelectionStep;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Capco\AppBundle\Repository\ProposalStepPaperVoteCounterRepository;
use Capco\AppBundle\Security\ProposalVoter;
use Capco\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UpdatePaperVoteMutation implements MutationInterface
{
    public const PROPOSAL_NOT_FOUND = 'PROPOSAL_NOT_FOUND';
    public const STEP_NOT_FOUND = 'STEP_NOT_FOUND';

    private EntityManagerInterface $em;
    private GlobalIdResolver $resolver;
    private ProposalStepPaperVoteCounterRepository $repository;
    private GlobalIdResolver $globalIdResolver;
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(
        EntityManagerInterface $em,
        GlobalIdResolver $resolver,
        ProposalStepPaperVoteCounterRepository $repository,
        GlobalIdResolver $globalIdResolver,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->em = $em;
        $this->resolver = $resolver;
        $this->repository = $repository;
        $this->globalIdResolver = $globalIdResolver;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke(Argument $input, User $viewer): array
    {
        try {
            $proposal = $this->getProposal($input, $viewer);
            $step = $this->getStep($input, $viewer);
            $paperVote = $this->getOrCreatePaperVote($proposal, $step);
            $paperVote->setTotalCount($input->offsetGet('count'));
            $paperVote->setTotalPointsCount($input->offsetGet('points'));
            $this->em->flush();
        } catch (UserError $error) {
            return ['error' => $error->getMessage()];
        }

        return ['proposal' => $proposal];
    }

    private function getOrCreatePaperVote(
        Proposal $proposal,
        AbstractStep $step
    ): ProposalStepPaperVoteCounter {
        $paperVote = $this->repository->findOneBy(['proposal' => $proposal, 'step' => $step]);
        if (null === $paperVote) {
            $paperVote = self::createPaperVote($proposal, $step);
            $this->em->persist($paperVote);
        }

        return $paperVote;
    }

    private function getStep(Argument $input, User $viewer): AbstractStep
    {
        $step = $this->resolver->resolve($input->offsetGet('step'), $viewer);
        if (!($step instanceof CollectStep) && !($step instanceof SelectionStep)) {
            throw new UserError(self::STEP_NOT_FOUND);
        }

        return $step;
    }

    private function getProposal(Argument $input, User $viewer): Proposal
    {
        $proposal = $this->resolver->resolve($input->offsetGet('proposal'), $viewer);

        if (null === $proposal) {
            throw new UserError(self::PROPOSAL_NOT_FOUND);
        }

        return $proposal;
    }

    private static function createPaperVote(
        Proposal $proposal,
        AbstractStep $step
    ): ProposalStepPaperVoteCounter {
        return (new ProposalStepPaperVoteCounter())
            ->setProposal($proposal)
            ->setStep($step)
            ->setTotalCount(0)
            ->setTotalPointsCount(0);
    }

    public function isGranted(string $proposalId, ?User $viewer = null): bool
    {
        if (!$viewer) {
            return false;
        }
        $proposal = $this->globalIdResolver->resolve($proposalId, $viewer);

        if ($proposal) {
            return $this->authorizationChecker->isGranted(ProposalVoter::EDIT, $proposal);
        }

        return false;
    }

}
