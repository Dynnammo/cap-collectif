<?php

namespace Application\Migrations;

use CapCollectif\IdToUuid\IdToUuidMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171011142013 extends IdToUuidMigration
{
    public function postUp(Schema $schema)
    {
        $this->migrate('question_choice');
    }
}
