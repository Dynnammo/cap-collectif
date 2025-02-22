<?php

namespace Capco\AppBundle\GraphQL\Resolver\Proposal;

use Psr\Log\LoggerInterface;
use Capco\AppBundle\Entity\Proposal;
use GraphQL\Executor\Promise\Promise;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Capco\AppBundle\GraphQL\Resolver\Traits\ResolverTrait;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;
use Capco\AppBundle\GraphQL\DataLoader\Proposal\ProposalViewerVoteDataLoader;

class ProposalViewerVoteResolver implements ResolverInterface
{
    use ResolverTrait;
    private $logger;
    private $proposalViewerVoteDataLoader;

    public function __construct(
        ProposalViewerVoteDataLoader $proposalViewerVoteDataLoader,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->proposalViewerVoteDataLoader = $proposalViewerVoteDataLoader;
    }

    public function __invoke(Proposal $proposal, Arg $args, $viewer): Promise
    {
        $user = $this->preventNullableViewer($viewer);

        try {
            $stepId = $args->offsetGet('step');

            return $this->proposalViewerVoteDataLoader->load(compact('proposal', 'stepId', 'user'));
        } catch (\RuntimeException $exception) {
            $this->logger->error(__METHOD__ . ' : ' . $exception->getMessage());

            throw new \RuntimeException($exception->getMessage());
        }
    }
}
