<?php

namespace Capco\AppBundle\Notifier;

use Capco\AppBundle\Entity\Argument;
use Capco\AppBundle\GraphQL\Resolver\ConsultationResolver;
use Capco\AppBundle\GraphQL\Resolver\User\UserUrlResolver;
use Capco\AppBundle\GraphQL\Resolver\UserResolver;
use Capco\AppBundle\Mailer\MailerService;
use Capco\AppBundle\Mailer\Message\Argument\NewArgumentModeratorMessage;
use Capco\AppBundle\Mailer\Message\Argument\TrashedArgumentAuthorMessage;
use Capco\AppBundle\Mailer\Message\Argument\UpdateArgumentModeratorMessage;
use Capco\AppBundle\SiteParameter\Resolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ArgumentNotifier extends BaseNotifier
{
    protected $consultationResolver;
    protected $router;
    protected $translator;
    protected $userUrlResolver;

    public function __construct(
        MailerService $mailer,
        Resolver $siteParams,
        UserResolver $userResolver,
        ConsultationResolver $consultationResolver,
        RouterInterface $router,
        TranslatorInterface $translator,
        UserUrlResolver $userUrlResolver
    ) {
        parent::__construct($mailer, $siteParams, $userResolver);
        $this->consultationResolver = $consultationResolver;
        $this->router = $router;
        $this->translator = $translator;
        $this->userUrlResolver = $userUrlResolver;
    }

    public function onCreation(Argument $argument)
    {
        $step = $argument->getStep();

        if ($step->isModeratingOnCreate()) {
            $this->mailer->sendMessage(
                NewArgumentModeratorMessage::create(
                    $argument,
                    $this->siteParams->getValue('admin.mail.notifications.receive_address'),
                    null,
                    $this->consultationResolver->resolveArgumentUrl($argument),
                    $this->userUrlResolver->__invoke($argument->getAuthor()),
                    $this->router,
                    $this->translator
                )
            );
        }
    }

    public function onUpdate(Argument $argument)
    {
        $step = $argument->getStep();

        if ($step->isModeratingOnUpdate()) {
            $this->mailer->sendMessage(
                UpdateArgumentModeratorMessage::create(
                    $argument,
                    $this->siteParams->getValue('admin.mail.notifications.receive_address'),
                    null,
                    $this->consultationResolver->resolveArgumentUrl($argument),
                    $this->userUrlResolver->__invoke($argument->getAuthor()),
                    $this->router,
                    $this->translator
                )
            );
        }
    }

    public function onTrash(Argument $argument)
    {
        $this->mailer->sendMessage(
            TrashedArgumentAuthorMessage::create(
                $argument,
                $this->consultationResolver->resolveArgumentUrl($argument)
            )
        );
    }
}
