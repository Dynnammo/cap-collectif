<?php

namespace Capco\AppBundle\GraphQL\Mutation\District;

use Capco\AppBundle\Form\ProjectDistrictType;
use Capco\AppBundle\GraphQL\Exceptions\GraphQLException;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Error\UserError;
use Capco\AppBundle\Repository\ProjectDistrictRepository;
use Symfony\Component\Form\FormFactory;

class UpdateProjectDistrictMutation implements MutationInterface
{
    protected $logger;
    protected $em;
    protected $projectDistrictRepository;
    protected $formFactory;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        ProjectDistrictRepository $projectDistrictRepository,
        FormFactory $formFactory
    ) {
        $this->logger = $logger;
        $this->em = $em;
        $this->projectDistrictRepository = $projectDistrictRepository;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Argument $input): array
    {
        $values = $input->getRawArguments();
        $projectDistrictId = $input->offsetGet('id');

        $projectDistrict = $this->projectDistrictRepository->find($projectDistrictId);

        if (!$projectDistrict) {
            throw new UserError(
                sprintf('Unknown project district with id: %s', $projectDistrictId)
            );
        }

        unset($values['id']);

        $form = $this->formFactory->create(ProjectDistrictType::class, $projectDistrict);
        $form->submit($values, false);

        if (!$form->isValid()) {
            throw GraphQLException::fromFormErrors($form);
        }

        $this->em->flush();

        return ['district' => $projectDistrict];
    }
}
