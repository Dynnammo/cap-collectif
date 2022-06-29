<?php

namespace Capco\AppBundle\Slack;

use Capco\AppBundle\Entity\EmailingCampaign;

class OmarDjinn extends AbstractSlackMessager
{
    private ?string $hook;

    public function __construct(?string $hook)
    {
        $this->hook = $hook;
    }

    public function sendBefore(EmailingCampaign $emailingCampaign): void
    {
        $this->send(
            'Bonjour grands princes, la campagne ' .
                $emailingCampaign->getName() .
                ' doit être lancée à ' .
                $emailingCampaign->getSendAt()->format('H:i:s') .
                ", je vous préviens dès qu'elle est terminée."
        );
    }

    public function sendAfter(int $count): void
    {
        $this->send(
            'Merci de votre patience les kheys, la campagne a été envoyée à ' .
                $count .
                ' destinataires'
        );
    }

    public function sendFail(): void
    {
        $this->send("Aie, ça s'est mal passé, vous devriez jeter un coup d'oeil");
    }

    protected function getHook(): string
    {
        return 'https://hooks.slack.com/services/' . $this->hook;
    }
}
