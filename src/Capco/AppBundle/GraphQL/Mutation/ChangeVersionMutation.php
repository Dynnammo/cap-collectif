<?php

namespace Capco\AppBundle\GraphQL\Mutation;

use Capco\AppBundle\Entity\OpinionVersion;
use Capco\AppBundle\Form\OpinionVersionType;
use Capco\AppBundle\GraphQL\Exceptions\GraphQLException;
use Capco\AppBundle\Helper\RedisStorageHelper;
use Capco\AppBundle\Repository\OpinionVersionRepository;
use Capco\UserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Argument as Arg;
use Overblog\GraphQLBundle\Error\UserError;
use Overblog\GraphQLBundle\Relay\Node\GlobalId;
use Symfony\Component\Form\FormFactoryInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class ChangeVersionMutation implements MutationInterface
{
    private $em;
    private $versionRepo;
    private $formFactory;
    private $redisStorage;

    public function __construct(
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
        OpinionVersionRepository $versionRepo,
        RedisStorageHelper $redisStorage
    ) {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->versionRepo = $versionRepo;
        $this->redisStorage = $redisStorage;
    }

    public function __invoke(Arg $input, User $user): array
    {
        $versionId = GlobalId::fromGlobalId($input->offsetGet('versionId'))['id'];
        /** @var OpinionVersion $version */
        $version = $this->versionRepo->find($versionId);

        if (!$version) {
            throw new UserError('Unknown version with id: ' . $versionId);
        }

        if ($user !== $version->getAuthor()) {
            throw new UserError("Can't update the version of someone else.");
        }

        if (!$version->canContribute($user)) {
            throw new UserError("Can't update uncontributable version.");
        }

        $values = $input->getArrayCopy();
        unset($values['versionId']);

        $form = $this->formFactory->create(OpinionVersionType::class, $version);
        $form->submit($values, false);

        if (!$form->isValid()) {
            throw GraphQLException::fromFormErrors($form);
        }

        $version->resetVotes();

        $this->em->flush();

        return ['version' => $version];
    }
}
