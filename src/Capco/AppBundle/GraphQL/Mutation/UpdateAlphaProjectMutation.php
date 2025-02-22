<?php

namespace Capco\AppBundle\GraphQL\Mutation;

use Doctrine\ORM\EntityManagerInterface;
use Capco\AppBundle\Form\Persister\ProjectPersister;
use Capco\AppBundle\Repository\ProjectRepository;
use Capco\AppBundle\Security\ProjectVoter;
use Capco\UserBundle\Entity\User;
use Capco\AppBundle\Entity\Project;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Overblog\GraphQLBundle\Relay\Node\GlobalId;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UpdateAlphaProjectMutation implements MutationInterface
{
    private ProjectPersister $persister;
    private ProjectRepository $projectRepository;
    private AuthorizationCheckerInterface $authorizationChecker;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProjectPersister $persister,
        ProjectRepository $projectRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->entityManager = $entityManager;
        $this->persister = $persister;
        $this->projectRepository = $projectRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke(Argument $input, User $viewer): array
    {
        $project = $this->persister->persist($input, $viewer, true);

        $this->invalidateCache($project);

        return ['project' => $project];
    }

    public function invalidateCache(Project $project): void
    {
        $slug = $project->getSlug();
        $cacheDriver = $this->entityManager->getConfiguration()->getResultCacheImpl();
        $cacheDriver->delete(ProjectRepository::getOneWithoutVisibilityCacheKey($slug));
    }

    public function isGranted(string $projectId): bool
    {
        $project = $this->projectRepository->find(GlobalId::fromGlobalId($projectId)['id']);

        if ($project) {
            return $this->authorizationChecker->isGranted(ProjectVoter::EDIT, $project);
        }

        return false;
    }
}
