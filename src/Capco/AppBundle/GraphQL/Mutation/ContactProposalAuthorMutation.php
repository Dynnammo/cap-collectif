<?php

namespace Capco\AppBundle\GraphQL\Mutation;

use Capco\AppBundle\Entity\Proposal;
use Capco\AppBundle\Enum\ContactProposalAuthorErrorCode;
use Capco\AppBundle\Mailer\MailerService;
use Capco\AppBundle\Mailer\Message\Proposal\ContactProposalAuthorMessage;
use Capco\AppBundle\Repository\ProposalRepository;
use Capco\AppBundle\Security\CaptchaChecker;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Relay\Node\GlobalId;
use Capco\AppBundle\Utils\RequestGuesser;

class ContactProposalAuthorMutation implements MutationInterface
{
    private ProposalRepository $proposalRepository;
    private MailerService $mailerService;
    private CaptchaChecker $captchaChecker;
    private EmailValidator $emailValidator;
    private RequestGuesser $requestGuesser;

    public function __construct(
        ProposalRepository $proposalRepository,
        MailerService $mailerService,
        CaptchaChecker $captchaChecker,
        EmailValidator $emailValidator,
        RequestGuesser $requestGuesser
    ) {
        $this->proposalRepository = $proposalRepository;
        $this->mailerService = $mailerService;
        $this->captchaChecker = $captchaChecker;
        $this->emailValidator = $emailValidator;
        $this->requestGuesser = $requestGuesser;
    }

    public function __invoke(Argument $argument): array
    {
        $proposal = $this->getProposal($argument);
        $errorLog = $this->getErrorLog(
            $proposal,
            $argument->offsetGet('captcha'),
            $this->requestGuesser->getClientIp()
        );

        if (null === $errorLog) {
            $this->sendContactMail(
                $proposal,
                $argument->offsetGet('senderName'),
                $argument->offsetGet('replyEmail'),
                $argument->offsetGet('message')
            );
        }

        return ['error' => $errorLog];
    }

    private function getErrorLog(?Proposal $proposal, string $captcha, string $ip): ?string
    {
        if (!$this->captchaChecker->__invoke($captcha, $ip)) {
            return ContactProposalAuthorErrorCode::INVALID_CAPTCHA;
        }
        if (!$proposal) {
            return ContactProposalAuthorErrorCode::NON_EXISTING_PROPOSAL;
        }
        if (!$proposal->getForm()->canContact()) {
            return ContactProposalAuthorErrorCode::NO_CONTACT_PROPOSAL;
        }
        if (
            !$this->emailValidator->isValid($proposal->getAuthor()->getEmail(), new RFCValidation())
        ) {
            return ContactProposalAuthorErrorCode::INVALID_EMAIL_AUTHOR;
        }

        return null;
    }

    private function sendContactMail(
        Proposal $proposal,
        string $senderName,
        string $senderEmail,
        string $message
    ): void {
        $this->mailerService->createAndSendMessage(
            ContactProposalAuthorMessage::class,
            $proposal,
            [
                'sender' => [
                    'name' => $senderName,
                    'email' => $senderEmail,
                ],
                'senderMessage' => $message,
                //'copyToAdmin' => true, @todo uncomment when viewer can consent to this copy.
            ],
            $proposal->getAuthor()
        );
    }

    private function getProposal(Argument $argument): ?Proposal
    {
        return $this->proposalRepository->find(
            GlobalId::fromGlobalId($argument->offsetGet('proposalId'))['id']
        );
    }
}
