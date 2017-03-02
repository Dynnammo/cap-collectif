<?php

namespace Capco\AppBundle\Entity;

use Capco\AppBundle\Entity\Questions\QuestionnaireAbstractQuestion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Capco\AppBundle\Traits\UuidTrait;

/**
 * @ORM\Table(name="registration_form")
 * @ORM\Entity(repositoryClass="Capco\AppBundle\Repository\RegistrationFormRepository")
 */
class RegistrationForm
{
    use UuidTrait;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\Questions\QuestionnaireAbstractQuestion", mappedBy="registrationForm", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getRealQuestions()
    {
        $questions = [];
        foreach ($this->questions as $qaq) {
            $questions[] = $qaq->getQuestion();
        }

        return $questions;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function setQuestions($questions)
    {
        foreach ($questions as $question) {
            $question->setRegistrationForm($this);
        }
        $this->questions = $questions;

        return $this;
    }

    public function addQuestion(QuestionnaireAbstractQuestion $question)
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
        }
        $question->setRegistrationForm($this);

        return $this;
    }

    public function removeQuestion(QuestionnaireAbstractQuestion $question)
    {
        $this->questions->removeElement($question);
        $question->setRegistrationForm(null);

        return $this;
    }
}
