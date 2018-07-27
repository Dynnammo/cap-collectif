<?php
namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Version20170613093358 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
    }

    public function postUp(Schema $schema)
    {
        $toggleManager = $this->container->get('capco.toggle.manager');
        $toggleManager->activate('captcha');
    }

    public function down(Schema $schema)
    {
    }
}
