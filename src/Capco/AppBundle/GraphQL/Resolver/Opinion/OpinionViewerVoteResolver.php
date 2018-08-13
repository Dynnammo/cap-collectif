<?php
namespace Capco\AppBundle\GraphQL\Resolver\Opinion;

use Psr\Log\LoggerInterface;
use Capco\UserBundle\Entity\User;
use Capco\AppBundle\Entity\Opinion;
use Capco\AppBundle\Entity\OpinionVersion;
use Capco\AppBundle\Entity\Interfaces\OpinionContributionInterface;
use Capco\AppBundle\Entity\AbstractVote;
use Capco\AppBundle\Repository\OpinionVoteRepository;
use Capco\AppBundle\Repository\OpinionVersionVoteRepository;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class OpinionViewerVoteResolver implements ResolverInterface
{
    private $logger;
    private $opinionVoteRepository;
    private $versionVoteRepository;

    public function __construct(
        OpinionVoteRepository $opinionVoteRepository,
        OpinionVersionVoteRepository $versionVoteRepository,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->opinionVoteRepository = $opinionVoteRepository;
        $this->versionVoteRepository = $versionVoteRepository;
    }

    public function __invoke(
        OpinionContributionInterface $contribution,
        ?User $user
    ): ?AbstractVote {
        if (!$user) {
            return null;
        }

        try {
            if ($contribution instanceof Opinion) {
                return $this->opinionVoteRepository->getByAuthorAndOpinion($user, $contribution);
            }
            if ($contribution instanceof OpinionVersion) {
                return $this->versionVoteRepository->getByAuthorAndOpinion($user, $contribution);
            }
            return null;
        } catch (\RuntimeException $exception) {
            $this->logger->error(__METHOD__ . ' : ' . $exception->getMessage());
            throw new \RuntimeException($exception->getMessage());
        }
    }
}
