<?php

namespace Capco\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Capco\AppBundle\Traits\TrashableTrait;
use Capco\AppBundle\Traits\SluggableTitleTrait;
use Capco\AppBundle\Traits\VotableTrait;
use Capco\UserBundle\Entity\User;

/**
 * Opinion.
 *
 * @ORM\Table(name="opinion",indexes={@ORM\Index(name="idx_enabled", columns={"id", "enabled"})})
 * @ORM\Entity(repositoryClass="Capco\AppBundle\Repository\OpinionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Opinion
{
    use TrashableTrait;
    use SluggableTitleTrait;
    use VotableTrait;

    public static $sortCriterias = [
        'positions' => 'opinion.sort.positions',
        'last' => 'opinion.sort.last',
        'old' => 'opinion.sort.old',
        'favorable' => 'opinion.sort.favorable',
        'votes' => 'opinion.sort.votes',
        'comments' => 'opinion.sort.comments',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $isEnabled = true;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="change", field={"title", "body", "appendices"})
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(name="body", type="text")
     * @Assert\NotBlank()
     */
    protected $body;

    /**
     * @ORM\Column(name="position", type="integer")
     */
    protected $position = 0;

    /**
     * @ORM\Column(name="versions_count", type="integer")
     */
    protected $versionsCount = 0;

    /**
     * @ORM\Column(name="sources_count", type="integer")
     */
    protected $sourcesCount = 0;

    /**
     * @ORM\Column(name="arguments_count", type="integer")
     */
    protected $argumentsCount = 0;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Capco\UserBundle\Entity\User", inversedBy="opinions")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $Author;

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\OpinionType", inversedBy="Opinions", cascade={"persist"})
     * @ORM\JoinColumn(name="opinion_type_id", referencedColumnName="id", nullable=false)
     */
    private $OpinionType;

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\ConsultationStep", inversedBy="opinions", cascade={"persist"})
     * @ORM\JoinColumn(name="step_id", referencedColumnName="id", nullable=true)
     * @Assert\NotNull()
     */
    private $step;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\Source", mappedBy="Opinion",  cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    protected $Sources;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\Argument", mappedBy="opinion",  cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    protected $arguments;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\OpinionModal", mappedBy="opinion",  cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $modals;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\OpinionVote", mappedBy="opinion", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $votes;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\Reporting", mappedBy="Opinion", cascade={"persist", "remove"})
     */
    protected $Reports;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\OpinionVersion", mappedBy="parent", cascade={"persist", "remove"})
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    private $versions;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\OpinionAppendix", mappedBy="opinion",  cascade={"persist", "remove"})
     */
    protected $appendices;

    /**
     * @ORM\Column(name="pinned", type="boolean")
     */
    protected $pinned = false;

    /**
     * @ORM\Column(name="ranking", type="integer", nullable=true)
     */
    protected $ranking = null;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->Reports = new ArrayCollection();
        $this->arguments = new ArrayCollection();
        $this->Sources = new ArrayCollection();
        $this->versions = new ArrayCollection();
        $this->appendices = new ArrayCollection();

        $this->updatedAt = new \Datetime();
        $this->createdAt = new \Datetime();

        $this->argumentsCount = 0;
        $this->sourcesCount = 0;
    }

    public function __toString()
    {
        if ($this->id) {
            return $this->getTitle();
        }

        return 'New opinion';
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get isEnabled.
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set isEnabled.
     *
     * @param bool $isEnabled
     *
     * @return Argument
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

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

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return mixed
     */
    public function getVersionsCount()
    {
        return $this->versionsCount;
    }

    /**
     * @param mixed $versionsCount
     */
    public function setVersionsCount($versionsCount)
    {
        $this->versionsCount = $versionsCount;
    }

    /**
     * @return int
     */
    public function getSourcesCount()
    {
        return $this->sourcesCount;
    }

    /**
     * @param int $sourcesCount
     */
    public function setSourcesCount($sourcesCount)
    {
        $this->sourcesCount = $sourcesCount;
    }

    /**
     * @return int
     */
    public function getArgumentsCount()
    {
        return $this->argumentsCount;
    }

    /**
     * @param int $argumentsCount
     */
    public function setArgumentsCount($argumentsCount)
    {
        $this->argumentsCount = $argumentsCount;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->Author;
    }

    /**
     * @param string $Author
     */
    public function setAuthor($Author)
    {
        $this->Author = $Author;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOpinionType()
    {
        return $this->OpinionType;
    }

    /**
     * @param mixed $OpinionType
     */
    public function setOpinionType($OpinionType)
    {
        $this->OpinionType = $OpinionType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param mixed $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * @return mixed
     */
    public function getSources()
    {
        return $this->Sources;
    }

    /**
     * @param $source
     *
     * @return $this
     */
    public function addSource($source)
    {
        if (!$this->Sources->contains($source)) {
            $this->Sources->add($source);
        }

        return $this;
    }

    /**
     * @param $source
     *
     * @return $this
     */
    public function removeSource($source)
    {
        $this->Sources->removeElement($source);

        return $this;
    }

    /**
     * Get arguments.
     *
     * @return ArrayCollection
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $argument
     *
     * @return $this
     */
    public function addArgument(Argument $argument)
    {
        if (!$this->arguments->contains($argument)) {
            $this->arguments->add($argument);
        }

        return $this;
    }

    /**
     * @param Argument $argument
     *
     * @return $this
     */
    public function removeArgument(Argument $argument)
    {
        $this->arguments->removeElement($argument);

        return $this;
    }

    public function getModals()
    {
        return $this->modals;
    }

    public function addModal(OpinionModal $modal)
    {
        if (!$this->modals->contains($modal)) {
            $this->modals->add($modal);
        }

        return $this;
    }

    public function removeModal(OpinionModal $modal)
    {
        $this->modals->removeElement($modal);

        return $this;
    }

    /**
     * Get votes.
     *
     * @return string
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param OpinionVote $vote
     *
     * @return $this
     */
    public function addVote($vote)
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
        }

        return $this;
    }

    /**
     * @param OpinionVote $vote
     *
     * @return $this
     */
    public function removeVote(OpinionVote $vote)
    {
        $this->votes->removeElement($vote);

        return $this;
    }

    /**
     * @return string
     */
    public function getReports()
    {
        return $this->Reports;
    }

    /**
     * @param Reporting $report
     *
     * @return $this
     */
    public function addReport(Reporting $report)
    {
        if (!$this->Reports->contains($report)) {
            $this->Reports->add($report);
        }

        return $this;
    }

    /**
     * @param Reporting $report
     *
     * @return $this
     */
    public function removeReport(Reporting $report)
    {
        $this->Reports->removeElement($report);

        return $this;
    }

    public function getVersions()
    {
        return $this->versions;
    }

    public function addVersion(Opinion $version)
    {
        if (!$this->versions->contains($version)) {
            $this->versions->add($version);
        }

        return $this;
    }

    public function removeVersion(Opinion $version)
    {
        $this->versions->removeElement($version);

        return $this;
    }

    public function getAppendices()
    {
        return $this->appendices;
    }

    public function setAppendices($appendices)
    {
        $this->appendices = $appendices;

        return $this;
    }

    public function addAppendice(OpinionAppendix $appendix)
    {
        if (!$this->appendices->contains($appendix)) {
            $appendix->setOpinion($this);
            $this->appendices->add($appendix);
        }

        return $this;
    }

    public function removeAppendice(OpinionAppendix $appendix)
    {
        $this->appendices->removeElement($appendix);

        return $this;
    }

    /**
     * @return bool
     */
    public function isPinned()
    {
        return $this->pinned;
    }

    /**
     * @param bool $pinned
     */
    public function setPinned($pinned)
    {
        $this->pinned = $pinned;
    }

    /**
     * @return mixed
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * @param mixed $ranking
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
    }

    // ******************************* Custom methods **************************************

    public function getVoteValueByUser(User $user)
    {
        foreach ($this->votes as $vote) {
            if ($vote->getUser() === $user && $vote->isConfirmed()) {
                return $vote->getValue();
            }
        }

        return;
    }

    public function userHasReport(User $user)
    {
        foreach ($this->Reports as $report) {
            if ($report->getReporter() == $user) {
                return true;
            }
        }

        return false;
    }

    // Used by elasticsearch for indexing
    public function getStrippedBody()
    {
        return strip_tags(html_entity_decode($this->body, ENT_QUOTES | ENT_HTML401, 'UTF-8'));
    }

    public function getArgumentForCount()
    {
        $i = 0;
        foreach ($this->arguments as $argument) {
            if ($argument->getType() === Argument::TYPE_FOR) {
                ++$i;
            }
        }

        return $i;
    }

    public function getArgumentAgainstCount()
    {
        $i = 0;
        foreach ($this->arguments as $argument) {
            if ($argument->getType() === Argument::TYPE_AGAINST) {
                ++$i;
            }
        }

        return $i;
    }

    /**
     * Increase count for opinion Vote.
     *
     * @param $type
     */
    public function increaseVotesCount($type)
    {
        if ($type == OpinionVote::$voteTypes['ok']) {
            ++$this->voteCountOk;

            return;
        }
        if ($type == OpinionVote::$voteTypes['nok']) {
            ++$this->voteCountNok;

            return;
        }
        if ($type == OpinionVote::$voteTypes['mitige']) {
            ++$this->voteCountMitige;
        }
    }

    /**
     * Decrease count for opinion Vote.
     *
     * @param $type
     */
    public function decreaseVotesCount($type)
    {
        if ($type == OpinionVote::$voteTypes['ok']) {
            --$this->voteCountOk;

            return;
        }
        if ($type == OpinionVote::$voteTypes['nok']) {
            --$this->voteCountNok;

            return;
        }
        if ($type == OpinionVote::$voteTypes['mitige']) {
            --$this->voteCountMitige;
        }
    }

    public function resetVotes()
    {
        foreach ($this->votes as $vote) {
            $this->removeVote($vote);
        }
        $this->voteCountMitige = 0;
        $this->voteCountNok = 0;
        $this->voteCountOk = 0;

        return $this;
    }

    /**
     * @param $type
     *
     * @return int
     */
    public function getArgumentsCountByType($type)
    {
        $count = 0;
        foreach ($this->arguments as $arg) {
            if (Argument::$argumentTypes[$arg->getType()] == $type) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @return bool
     */
    public function canDisplay()
    {
        return $this->isEnabled && $this->step->canDisplay();
    }

    /**
     * @return bool
     */
    public function canContribute()
    {
        return $this->isEnabled && !$this->isTrashed && $this->step->canContribute();
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->isEnabled && !$this->isTrashed;
    }

    /**
     * @param int $nb
     *
     * @return string
     */
    public function getBodyExcerpt($nb = 100)
    {
        $excerpt = substr($this->body, 0, $nb);
        $excerpt = $excerpt.'...';

        return $excerpt;
    }

    public function getSortedAppendices()
    {
        $iterator = $this->appendices->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($this->getPositionForAppendixType($a->getAppendixType()) < $this->getPositionForAppendixType($b->getAppendixType())) ? -1 : 1;
        });

        return iterator_to_array($iterator);
    }

    public function getPositionForAppendixType($at)
    {
        foreach ($this->getOpinionType()->getAppendixTypes() as $otat) {
            if ($otat->getAppendixType()->getId() === $at->getId()) {
                return $otat->getPosition();
            }
        }

        return 0;
    }

    public function canAddVersions()
    {
        if ($this->getOpinionType()) {
            return $this->getOpinionType()->isVersionable();
        }

        return false;
    }

    public function canAddSources()
    {
        if ($this->getOpinionType()) {
            return $this->getOpinionType()->isSourceable();
        }

        return false;
    }

    public function getCommentSystem()
    {
        if ($this->getOpinionType()) {
            return $this->getOpinionType()->getCommentSystem();
        }

        return;
    }

    public function canAddComments()
    {
        $cs = $this->getCommentSystem();

        return $cs === 1 || $cs === 2;
    }

    // ******************* Lifecycle *********************************

    /**
     * @ORM\PreRemove
     */
    public function deleteOpinion()
    {
        if ($this->step != null) {
            $this->step->removeOpinion($this);
        }
        if ($this->OpinionType != null) {
            $this->OpinionType->removeOpinion($this);
        }
    }
}
