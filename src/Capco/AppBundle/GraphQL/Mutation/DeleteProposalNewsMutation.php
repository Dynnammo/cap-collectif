<?php

namespace Capco\AppBundle\GraphQL\Mutation;

use Capco\AppBundle\Entity\NotificationsConfiguration\ProposalFormNotificationConfiguration;
use Capco\AppBundle\Entity\Post;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Capco\AppBundle\GraphQL\Resolver\Proposal\ProposalUrlResolver;
use Capco\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Error\UserError;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Psr\Log\LoggerInterface;
use Swarrot\Broker\Message;
use Swarrot\SwarrotBundle\Broker\Publisher;
use Symfony\Component\HttpFoundation\RequestStack;

class DeleteProposalNewsMutation implements MutationInterface
{
    public const POST_NOT_FOUND = 'POST_NOT_FOUND';
    public const ACCESS_DENIED = 'ACCESS_DENIED';

    private EntityManagerInterface $em;
    private GlobalIdResolver $globalIdResolver;
    private LoggerInterface $logger;
    private Publisher $publisher;
    private ProposalUrlResolver $proposalUrlResolver;
    private RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        GlobalIdResolver $globalIdResolver,
        LoggerInterface $logger,
        Publisher $publisher,
        ProposalUrlResolver $proposalUrlResolver,
        RequestStack $requestStack
    ) {
        $this->em = $em;
        $this->globalIdResolver = $globalIdResolver;
        $this->logger = $logger;
        $this->publisher = $publisher;
        $this->proposalUrlResolver = $proposalUrlResolver;
        $this->requestStack = $requestStack;
    }

    public function __invoke(Arg $input, User $viewer): array
    {
        try {
            $proposalPost = $this->getPost($input, $viewer);
            $id = $input->offsetGet('postId');
            $proposal = $proposalPost->getProposals()->first();
            if(!$proposal) {
                throw new UserError(UpdateProposalNewsMutation::PROPOSAL_NOT_FOUND);
            }
            $proposalName = $proposal->getTitle();
            $projectName = $proposal->getProject()->getTitle();
            $proposalPostAuthor = $proposalPost
                ->getAuthorsObject()
                ->first()
                ->getDisplayname();
            $proposalUrl = $this->proposalUrlResolver->__invoke(
                $proposalPost->getProposals()->first(),
                $this->requestStack
            );

            /** @var ProposalFormNotificationConfiguration $config */
            $config = $proposalPost
                ->getProposals()
                ->first()
                ->getProposalForm()
                ->getNotificationsConfiguration();
            $this->em->remove($proposalPost);
            $this->em->flush();

            if ($config->isOnProposalNewsDelete()) {
                $this->publisher->publish(
                    'proposal_news.delete',
                    new Message(
                        json_encode([
                            'postId' => $id,
                            'proposalName' => $proposalName,
                            'projectName' => $projectName,
                            'postAuthor' => $proposalPostAuthor,
                        ])
                    )
                );
            }

            return ['postId' => $id, 'proposalUrl' => $proposalUrl, 'errorCode' => null];
        } catch (UserError $error) {
            return ['errorCode' => $error->getMessage()];
        }
    }

    private function getPost(Arg $input, User $viewer): Post
    {
        $proposalPostGlobalId = $input->offsetGet('postId');
        $proposalPost = $this->globalIdResolver->resolve($proposalPostGlobalId, $viewer);
        if (!$proposalPost || !$proposalPost instanceof Post) {
            $this->logger->error('Unknown post with id: ' . $proposalPostGlobalId);

            throw new UserError(self::POST_NOT_FOUND);
        }
        if (!$proposalPost->isAuthor($viewer) && !$viewer->isAdmin()) {
            throw new UserError(self::ACCESS_DENIED);
        }

        return $proposalPost;
    }
}
