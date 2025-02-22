<?php

namespace Capco\AppBundle\GraphQL\Mutation\Sms;

use Capco\AppBundle\Entity\PhoneToken;
use Capco\AppBundle\Entity\Steps\CollectStep;
use Capco\AppBundle\Entity\Steps\SelectionStep;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Capco\AppBundle\Helper\TwilioHelper;
use Capco\AppBundle\Repository\AnonymousUserProposalSmsVoteRepository;
use Capco\AppBundle\Repository\PhoneTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class VerifySmsVotePhoneNumberMutation implements MutationInterface
{
    public const TWILIO_API_ERROR = 'TWILIO_API_ERROR';

    private EntityManagerInterface $em;
    private TwilioHelper $twilioHelper;
    private AnonymousUserProposalSmsVoteRepository $anonymousUserProposalSmsVoteRepository;
    private GlobalIdResolver $globalIdResolver;
    private TokenGenerator $tokenGenerator;
    private PhoneTokenRepository $phoneTokenRepository;

    public function __construct(
        EntityManagerInterface $em,
        TwilioHelper $twilioHelper,
        GlobalIdResolver $globalIdResolver,
        AnonymousUserProposalSmsVoteRepository $anonymousUserProposalSmsVoteRepository,
        TokenGenerator $tokenGenerator,
        PhoneTokenRepository $phoneTokenRepository
    ) {
        $this->em = $em;
        $this->twilioHelper = $twilioHelper;
        $this->anonymousUserProposalSmsVoteRepository = $anonymousUserProposalSmsVoteRepository;
        $this->globalIdResolver = $globalIdResolver;
        $this->tokenGenerator = $tokenGenerator;
        $this->phoneTokenRepository = $phoneTokenRepository;
    }

    public function __invoke(Argument $input): array
    {
        $phone = $input->offsetGet('phone');
        $code = $input->offsetGet('code');
        $proposalId = $input->offsetGet('proposalId');
        $stepId = $input->offsetGet('stepId');

        $proposal = $this->globalIdResolver->resolve($proposalId, null);
        $step = $this->globalIdResolver->resolve($stepId, null);

        $verifyErrorCode = $this->twilioHelper->verifySms($phone, $code);
        if ($verifyErrorCode) {
            return ['errorCode' => $verifyErrorCode];
        }

        $anonUserProposalSmsVote = null;
        if ($step instanceof CollectStep) {
            $anonUserProposalSmsVote = $this->anonymousUserProposalSmsVoteRepository->findMostRecentSmsByCollectStep(
                $phone,
                $proposal,
                $step
            );
        } elseif ($step instanceof SelectionStep) {
            $anonUserProposalSmsVote = $this->anonymousUserProposalSmsVoteRepository->findMostRecentSmsBySelectionStep(
                $phone,
                $proposal,
                $step
            );
        }

        $token = $this->generateToken($phone);
        if ($anonUserProposalSmsVote) {
            $anonUserProposalSmsVote->setApproved();
            $this->em->flush();
        }

        return ['errorCode' => null, 'token' => $token];
    }

    public function generateToken(string $phone): string
    {
        $token = $this->tokenGenerator->generateToken();
        $phoneToken = $this->phoneTokenRepository->findOneBy(['phone' => $phone]);

        if ($phoneToken) {
            $phoneToken->setToken($token);
        } else {
            $phoneToken = (new PhoneToken())->setPhone($phone)->setToken($token);
        }

        $this->em->persist($phoneToken);
        $this->em->flush();

        return $token;
    }
}
