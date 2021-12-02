<?php

namespace Capco\AppBundle\Entity;

use Capco\AppBundle\Repository\UserInviteRepository;
use Capco\AppBundle\Traits\TimestampableTrait;
use Capco\AppBundle\Traits\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInviteRepository::class)
 * @ORM\Table(name="user_invite")
 */
class UserInvite
{
    use TimestampableTrait;
    use UuidTrait;

    public const EXPIRES_AT_PERIOD = '+ 7 days';

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $token;

    /**
     * @ORM\Column(name="expires_at", type="datetime")
     */
    private \DateTimeInterface $expiresAt;

    /**
     * @ORM\Column(name="is_admin", type="boolean")
     */
    private bool $isAdmin;

    /**
     * @ORM\Column(name="is_project_admin", type="boolean", options={"default": false})
     */
    private bool $isProjectAdmin = false;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="userInvites")
     * @ORM\JoinTable(name="user_invite_groups",
     *  joinColumns={@ORM\JoinColumn(name="user_invite_id", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $groups;

    /**
     * @ORM\Column(type="string", length=500 ,nullable=true, name="message")
     */
    private ?string $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="redirection_url")
     */
    private ?string $redirectionUrl;

    /**
     * @ORM\OneToMany(targetEntity="Capco\AppBundle\Entity\UserInviteEmailMessage", mappedBy="invitation", cascade={"persist"})
     */
    private $emailMessages;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->emailMessages = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function isProjectAdmin(): ?bool
    {
        return $this->isProjectAdmin;
    }

    public function setIsProjectAdmin(bool $isProjectAdmin): self
    {
        $this->isProjectAdmin = $isProjectAdmin;

        return $this;
    }

    public function hasExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function setGroups(ArrayCollection $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getRedirectionUrl(): ?string
    {
        return $this->redirectionUrl;
    }

    public function setRedirectionUrl(?string $redirectionUrl): self
    {
        $this->redirectionUrl = $redirectionUrl;

        return $this;
    }

    public function getEmailMessages(): Collection
    {
        return $this->emailMessages;
    }

    public function getRelaunchCount(): int
    {
        // We do not count the first email message as its count for
        // the first message sent when the invitation is created
        return $this->getEmailMessages()->count() - 1;
    }

    public function addEmailMessage(UserInviteEmailMessage $emailMessage): self
    {
        if (!$this->groups->contains($emailMessage)) {
            $this->emailMessages[] = $emailMessage;
        }

        return $this;
    }
}
