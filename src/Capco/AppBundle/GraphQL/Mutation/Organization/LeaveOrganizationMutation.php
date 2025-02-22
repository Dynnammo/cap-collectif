<?php

namespace Capco\AppBundle\GraphQL\Mutation\Organization;

use Capco\AppBundle\Entity\Organization\Organization;
use Capco\AppBundle\GraphQL\Resolver\GlobalIdResolver;
use Capco\AppBundle\Repository\Organization\OrganizationMemberRepository;
use Capco\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class LeaveOrganizationMutation implements MutationInterface
{
    public const ORGANIZATION_NOT_FOUND = 'ORGANIZATION_NOT_FOUND';
    private GlobalIdResolver $globalIdResolver;
    private EntityManagerInterface $entityManager;
    private OrganizationMemberRepository $repository;

    public function __construct(
        GlobalIdResolver $globalIdResolver,
        EntityManagerInterface $entityManager,
        OrganizationMemberRepository $repository
    ) {
        $this->globalIdResolver = $globalIdResolver;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    public function __invoke(Arg $input, User $viewer): array
    {
        $organizationId = $input->offsetGet('organizationId');
        $organization = $this->globalIdResolver->resolve($organizationId, $viewer);

        if (!$organization instanceof Organization) {
            return ['errorCode' => self::ORGANIZATION_NOT_FOUND];
        }
        $organizations = $viewer->removeOrganization($organization);
        $organizationMember = $this->repository->findOneBy([
            'user' => $viewer,
            'organization' => $organization,
        ]);
        $this->entityManager->remove($organizationMember);
        $this->entityManager->flush();

        return ['errorCode' => null, 'organizations' => $organizations];
    }
}
