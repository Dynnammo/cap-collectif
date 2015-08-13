<?php

namespace Capco\AppBundle\Entity;

use Capco\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Capco\AppBundle\Traits\EnableTrait;
use Capco\AppBundle\Traits\SluggableTitleTrait;
use Capco\AppBundle\Traits\TimestampableTrait;

/**
 * Source.
 *
 * @ORM\Table(name="opinion_additional_element")
 * @ORM\Entity(repositoryClass="Capco\AppBundle\Repository\OpinionAdditionalElementRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OpinionAdditionalElement
{
    use EnableTrait;
    use SluggableTitleTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="body", type="text")
     * @Assert\NotBlank()
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\Opinion", inversedBy="additionalElements", cascade={"persist"})
     * @ORM\JoinColumn(name="opinion_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $opinion;

    public function __toString()
    {
        if ($this->id) {
            return $this->getTitle();
        } else {
            return 'New additional element';
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function getOpinion()
    {
        return $this->opinion;
    }

    public function setOpinion($opinion)
    {
        $this->opinion = $opinion;

        return $this;
    }
}
