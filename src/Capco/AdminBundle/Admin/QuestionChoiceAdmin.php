<?php

namespace Capco\AdminBundle\Admin;

use Capco\AppBundle\Entity\QuestionChoice;
use Capco\AppBundle\Repository\SiteColorRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QuestionChoiceAdmin extends AbstractAdmin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $colorsAvailable = QuestionChoice::$availableColors;
        $colorsAvailable[
            'color.btn.primary.bg'
        ] = $this->getConfigurationPool()
            ->getContainer()
            ->get(SiteColorRepository::class)
            ->findOneBy(['keyname' => 'color.btn.primary.bg'])
            ->getValue();

        $formMapper
            ->add('position', null, [
                'label' => 'global.position',
                'required' => true
            ])
            ->add('title', null, [
                'label' => 'global.title',
                'required' => true
            ])
            ->add('description', 'textarea', [
                'label' => 'global.description',
                'required' => false,
                'attr' => [
                    'class' => ''
                ]
            ])
            ->add('color', ChoiceType::class, [
                'label' => 'admin.fields.question_choice.color',
                'required' => false,
                'choices' => $colorsAvailable,
                'choices_as_values' => true,
                'choice_translation_domain' => 'CapcoAppBundle',
                'placeholder' => 'Choisir une couleur...'
            ])
            ->add(
                'image',
                'sonata_type_model_list',
                [
                    'label' => 'global.image',
                    'required' => false
                ],
                [
                    'link_parameters' => [
                        'context' => 'default',
                        'hide_context' => true,
                        'provider' => 'sonata.media.provider.image'
                    ]
                ]
            );
    }
}
