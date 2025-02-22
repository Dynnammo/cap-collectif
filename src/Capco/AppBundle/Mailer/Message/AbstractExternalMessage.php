<?php

namespace Capco\AppBundle\Mailer\Message;

use Capco\UserBundle\Entity\User;

abstract class AbstractExternalMessage extends AbstractMessage
{
    public const FOOTER = 'notification.email.external_footer';

    private User $recipient;

    public function __construct(
        string $recipientEmail,
        string $subject,
        array $subjectVars,
        string $template,
        array $templateVars,
        User $recipient,
        ?string $replyTo = null
    ) {
        parent::__construct(
            $recipientEmail,
            $recipient->getLocale(),
            $recipient->getUsername(),
            $subject,
            $subjectVars,
            $template,
            $templateVars,
            null,
            null,
            $replyTo
        );
        $this->recipient = $recipient;
    }

    public static function getMyFooterVars(
        string $recipientEmail = '',
        string $siteName = '',
        string $siteURL = ''
    ): array {
        return [
            '{to}' => $recipientEmail,
            '{sitename}' => $siteName,
            '{siteUrl}' => $siteURL,
            '{businessUrl}' => 'https://cap-collectif.com',
            '{business}' => 'Cap Collectif',
        ];
    }
}
