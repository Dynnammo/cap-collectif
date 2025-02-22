<?php

namespace Capco\AppBundle\GraphQL\Mutation\Debate;

use Capco\AppBundle\Elasticsearch\Indexer;
use Capco\AppBundle\Encoder\DebateAnonymousParticipationHashEncoder;
use Capco\AppBundle\Entity\Debate\Debate;
use Capco\AppBundle\Entity\Debate\DebateAnonymousArgument;
use Capco\AppBundle\Entity\Debate\DebateArgument;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Capco\AppBundle\Mailer\SendInBlue\SendInBlueManager;
use Capco\AppBundle\Notifier\DebateNotifier;
use Capco\AppBundle\Repository\Debate\DebateAnonymousArgumentRepository;
use Capco\AppBundle\Repository\DebateArgumentRepository;
use Capco\AppBundle\Security\DebateArgumentVoter;
use Capco\AppBundle\Validator\Constraints\CheckDebateAnonymousParticipationHashConstraint;
use Capco\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Capco\AppBundle\Utils\RequestGuesser;

class AbstractDebateArgumentMutation
{
    public const UNKNOWN_DEBATE = 'UNKNOWN_DEBATE';
    public const CLOSED_DEBATE = 'CLOSED_DEBATE';
    public const UNKNOWN_DEBATE_ARGUMENT = 'UNKNOWN_DEBATE_ARGUMENT';
    public const CANNOT_DELETE_DEBATE_ARGUMENT = 'CANNOT_DELETE_DEBATE_ARGUMENT';
    public const NOT_ARGUMENT_AUTHOR = 'NOT_ARGUMENT_AUTHOR';
    public const ALREADY_HAS_ARGUMENT = 'ALREADY_HAS_ARGUMENT';
    public const INVALID_HASH = 'INVALID_HASH';

    protected EntityManagerInterface $em;
    protected GlobalIdResolver $globalIdResolver;
    protected DebateArgumentRepository $repository;
    protected DebateAnonymousArgumentRepository $anonymousRepository;
    protected AuthorizationCheckerInterface $authorizationChecker;
    protected Indexer $indexer;
    protected ValidatorInterface $validator;
    protected TokenGeneratorInterface $tokenGenerator;
    protected DebateNotifier $debateNotifier;
    protected RequestGuesser $requestGuesser;
    protected SendInBlueManager $sendInBlueManager;
    protected Publisher $publisher;

    public function __construct(
        EntityManagerInterface $em,
        GlobalIdResolver $globalIdResolver,
        DebateArgumentRepository $repository,
        DebateAnonymousArgumentRepository $anonymousRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        Indexer $indexer,
        ValidatorInterface $validator,
        TokenGeneratorInterface $tokenGenerator,
        DebateNotifier $debateNotifier,
        RequestGuesser $requestGuesser,
        SendInBlueManager $sendInBlueManager,
        Publisher $publisher
    ) {
        $this->em = $em;
        $this->globalIdResolver = $globalIdResolver;
        $this->repository = $repository;
        $this->anonymousRepository = $anonymousRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->indexer = $indexer;
        $this->validator = $validator;
        $this->tokenGenerator = $tokenGenerator;
        $this->debateNotifier = $debateNotifier;
        $this->requestGuesser = $requestGuesser;
        $this->sendInBlueManager = $sendInBlueManager;
        $this->publisher = $publisher;
    }

    protected function getDebateFromInput(Arg $input, ?User $viewer): Debate
    {
        $debate = $this->globalIdResolver->resolve($input->offsetGet('debate'), $viewer);
        if (!($debate instanceof Debate)) {
            throw new UserError(self::UNKNOWN_DEBATE);
        }

        return $debate;
    }

    protected function getArgument(Arg $input, User $viewer): DebateArgument
    {
        $debateArgument = $this->globalIdResolver->resolve($input->offsetGet('id'), $viewer);
        if (!($debateArgument instanceof DebateArgument)) {
            throw new UserError(self::UNKNOWN_DEBATE_ARGUMENT);
        }

        return $debateArgument;
    }

    protected function checkCreateRights(Debate $debate, ?User $viewer, Arg $input): void
    {
        if ($viewer) {
            if ($this->repository->countByDebateAndUser($debate, $viewer)) {
                throw new UserError(self::ALREADY_HAS_ARGUMENT);
            }
        } else {
            if (
                $this->anonymousRepository->findOneBy([
                    'email' => $input->offsetGet('email'),
                    'debate' => $debate,
                ])
            ) {
                throw new UserError(self::ALREADY_HAS_ARGUMENT);
            }
        }
    }

    protected function checkUpdateRightsOnArgument(DebateArgument $argument): void
    {
        if (!$this->authorizationChecker->isGranted(DebateArgumentVoter::UPDATE, $argument)) {
            throw new UserError(self::NOT_ARGUMENT_AUTHOR);
        }
    }

    protected function checkDeleteRightsOnArgument(DebateArgument $argument): void
    {
        if (!$this->authorizationChecker->isGranted(DebateArgumentVoter::DELETE, $argument)) {
            throw new UserError(self::CANNOT_DELETE_DEBATE_ARGUMENT);
        }
    }

    protected static function checkDebateIsOpen(Debate $debate): void
    {
        if (!($debate->getStep() && $debate->getStep()->isOpen())) {
            throw new UserError(self::CLOSED_DEBATE);
        }
    }

    protected function getArgumentFromHash(Arg $input): DebateAnonymousArgument
    {
        $hash = $input->offsetGet('hash');
        $this->checkHash($hash);
        $argument = $this->anonymousRepository->findOneByHashData(
            (new DebateAnonymousParticipationHashEncoder())->decode($hash)
        );
        if (!$argument) {
            throw new UserError(self::UNKNOWN_DEBATE_ARGUMENT);
        }

        return $argument;
    }

    private function checkHash(string $hash): void
    {
        if (
            $this->validator
                ->validate($hash, [new CheckDebateAnonymousParticipationHashConstraint()])
                ->count() > 0
        ) {
            throw new UserError(self::INVALID_HASH);
        }
    }
}
