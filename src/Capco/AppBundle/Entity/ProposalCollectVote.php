<?php

namespace Capco\AppBundle\Entity;

use Capco\AppBundle\Entity\Steps\CollectStep;
use Doctrine\ORM\Mapping as ORM;
use Capco\AppBundle\Validator\Constraints as CapcoAssert;

/**
 * @ORM\Entity(repositoryClass="Capco\AppBundle\Repository\ProposalCollectVoteRepository")
 * @ORM\HasLifecycleCallbacks()
 * @CapcoAssert\HasAnonymousOrUser()
 * @CapcoAssert\EmailDoesNotBelongToUser(message="proposal.vote.email_belongs_to_user")
 * @CapcoAssert\DidNotAlreadyVote(message="proposal.vote.already_voted", repositoryPath="CapcoAppBundle:ProposalCollectVote", objectPath="proposal")
 * @CapcoAssert\HasEnoughCreditsToVote()
 */
class ProposalCollectVote extends AbstractVote
{
    use \Capco\AppBundle\Traits\AnonymousableTrait;
    use \Capco\AppBundle\Traits\PrivatableTrait;

    const ANONYMOUS = 'ANONYMOUS';

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\Proposal", inversedBy="collectVotes", cascade={"persist"})
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $proposal;

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\Steps\CollectStep", cascade={"persist"})
     * @ORM\JoinColumn(name="collect_step_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $collectStep;

    public function getCollectStep(): CollectStep
    {
        return $this->collectStep;
    }

    public function setCollectStep(CollectStep $collectStep): self
    {
        $this->collectStep = $collectStep;

        return $this;
    }

    public function getProposal(): Proposal
    {
        return $this->proposal;
    }

    public function setProposal(Proposal $proposal): self
    {
        $this->proposal = $proposal;
        $proposal->addCollectVote($this);

        return $this;
    }

    public function getRelatedEntity()
    {
        return $this->proposal;
    }

    /**
     * @ORM\PreRemove
     */
    public function deleteVote()
    {
        $this->proposal->removeCollectVote($this);
    }
}
