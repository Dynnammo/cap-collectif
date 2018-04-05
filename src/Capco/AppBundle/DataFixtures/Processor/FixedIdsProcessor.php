<?php

namespace Capco\AppBundle\DataFixtures\Processor;

use Capco\ClassificationBundle\Entity\Context;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nelmio\Alice\ProcessorInterface;

class FixedIdsProcessor implements ProcessorInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function preProcess($object)
    {
        if (!($object instanceof Context) && $object->getId()) {
            $metadata = $this->em->getClassMetadata(get_class($object));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess($object)
    {
    }
}
