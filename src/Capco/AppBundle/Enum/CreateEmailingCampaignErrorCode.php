<?php

namespace Capco\AppBundle\Enum;

class CreateEmailingCampaignErrorCode implements EnumType
{
    public const ID_NOT_FOUND_MAILING_LIST = 'ID_NOT_FOUND_MAILING_LIST';
    public const ID_NOT_FOUND_GROUP = 'ID_NOT_FOUND_GROUP';
    public const ALL = [self::ID_NOT_FOUND_MAILING_LIST, self::ID_NOT_FOUND_GROUP];

    public static function isValid($value): bool
    {
        return \in_array($value, self::ALL, true);
    }

    public static function getAvailableTypes(): array
    {
        return self::ALL;
    }

    public static function getAvailableTypesToString(): string
    {
        return implode(' | ', self::ALL);
    }
}
