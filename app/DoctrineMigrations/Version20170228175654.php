<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170228175654 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->connection->insert('site_parameter', [
            'keyname' => 'shield.introduction',
            'category' => 'pages.shield',
            'value' => '<p>Ce site est réservé aux inscrits</p>',
            'position' => 2,
            'enabled' => true,
            'type' => 1,
        ]);
        $this->connection->insert('site_image', ['keyname' => 'image.shield', 'category' => 'pages.shield', 'Media_id' => null, 'is_enabled' => true, 'position' => 1]);
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
