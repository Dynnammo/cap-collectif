<?php

namespace Capco\AppBundle\DTO\Analytics;

use Capco\UserBundle\Entity\User;

class AnalyticsContributor
{
    private User $user;
    /**
     * @var iterable|AnalyticsContributorContribution[]
     */
    private iterable $contributions = [];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function fromEs(array $contributions): self
    {
        $this->contributions = array_map(
            static fn(array $contribution) => AnalyticsContributorContribution::fromEs(
                $contribution
            ),
            $contributions
        );

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getContributions(?int $first): iterable
    {
        if ($first !== null && $first >= 0) {
            return array_slice($this->contributions, 0, $first);
        }

        return $this->contributions;
    }
}
