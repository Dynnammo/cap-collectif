<?php

namespace Capco\AppBundle\Entity\ContactForm;

use Capco\AppBundle\Traits\SluggableTitleTrait;
use Capco\AppBundle\Traits\TextableTrait;
use Doctrine\ORM\Mapping as ORM;
use Capco\AppBundle\Traits\UuidTrait;
use Capco\AppBundle\Model\Translation;
use Capco\AppBundle\Traits\TranslationTrait;

/**
 * @ORM\Entity(repositoryClass="Capco\AppBundle\Repository\ContactFormTranslationRepository")
 * @ORM\Entity()
 * @ORM\Table(
 *  name="contact_form_translation",
 *  uniqueConstraints={
 *    @ORM\UniqueConstraint(
 *      name="translation_unique",
 *      columns={"translatable_id", "locale"}
 *    )
 * })
 */
class ContactFormTranslation implements Translation
{
    use UuidTrait;
    use TextableTrait;
    use SluggableTitleTrait;
    use TranslationTrait;

    /**
     * @ORM\Column(name="confidentiality", type="text", nullable=true)
     */
    private $confidentiality;

    public function getConfidentiality(): ?string
    {
        return $this->confidentiality;
    }

    public function setConfidentiality(?string $confidentiality): self
    {
        $this->confidentiality = $confidentiality;

        return $this;
    }

    public static function getTranslatableEntityClass(): string
    {
        return ContactForm::class;
    }
}
