<?php

namespace Capco\AppBundle\Entity;

use Capco\AppBundle\Entity\Proposal;
use Capco\AppBundle\Entity\Steps\SelectionStep;
use Doctrine\ORM\Mapping as ORM;
use Capco\AppBundle\Validator\Constraints as CapcoAssert;

/**
 * @ORM\Entity(repositoryClass="Capco\AppBundle\Repository\ProposalSelectionVoteRepository")
 * @ORM\HasLifecycleCallbacks()
 * @CapcoAssert\HasAnonymousOrUser()
 * @CapcoAssert\EmailDoesNotBelongToUser(message="proposal.vote.email_belongs_to_user")
 * @CapcoAssert\DidNotAlreadyVote(message="proposal.vote.already_voted", repositoryPath="CapcoAppBundle:ProposalSelectionVote", objectPath="proposal")
 * @CapcoAssert\HasEnoughCreditsToVote()
 */
class ProposalSelectionVote extends AbstractVote
{
    use \Capco\AppBundle\Traits\AnonymousableTrait;
    use \Capco\AppBundle\Traits\PrivatableTrait;

    const ANONYMOUS = 'ANONYMOUS';

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\Proposal", inversedBy="selectionVotes", cascade={"persist"})
     * @ORM\JoinColumn(name="proposal_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $proposal;

    /**
     * @ORM\ManyToOne(targetEntity="Capco\AppBundle\Entity\Steps\SelectionStep", cascade={"persist"})
     * @ORM\JoinColumn(name="selection_step_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $selectionStep;

    public function getProposal(): Proposal
    {
        return $this->proposal;
    }

    public function setProposal(Proposal $proposal): self
    {
        $this->proposal = $proposal;
        $proposal->addSelectionVote($this);

        return $this;
    }

    public function getSelectionStep(): SelectionStep
    {
        return $this->selectionStep;
    }

    public function setSelectionStep(SelectionStep $selectionStep): self
    {
        $this->selectionStep = $selectionStep;

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
        $this->proposal->removeSelectionVote($this);
    }
}
