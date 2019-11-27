<?php

namespace Capco\AdminBundle\Admin;

use Capco\AppBundle\Entity\Event;
use Capco\AppBundle\Entity\HighlightedEvent;
use Capco\AppBundle\Entity\HighlightedPost;
use Capco\AppBundle\Entity\HighlightedProject;
use Capco\AppBundle\Entity\HighlightedTheme;
use Capco\AppBundle\Entity\Post;
use Capco\AppBundle\Entity\Project;
use Capco\AppBundle\Entity\Theme;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class HighlightedContentAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    ];

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('move_actions', 'actions', [
                'label' => 'admin.action.highlighted_content.move_actions.label',
                'template' => 'SonataAdminBundle:CRUD:list__action.html.twig',
                'type' => 'action',
                'code' => 'Action',
                'actions' => [
                    'up' => [
                        'template' => 'CapcoAdminBundle:HighlightedContent:list__action_up.html.twig',
                    ],
                    'down' => [
                        'template' => 'CapcoAdminBundle:HighlightedContent:list__action_down.html.twig',
                    ],
                ],
            ])
            ->add('object', null, [
                'label' => 'global.contenu',
                'mapped' => false,
                'template' => 'CapcoAdminBundle:HighlightedContent:list__object.html.twig',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper->add('position', null, [
            'label' => 'global.position',
        ]);

        if ($subject instanceof HighlightedPost) {
            $formMapper->add('post', 'sonata_type_model', [
                'label' => 'global.article',
                'class' => Post::class,
                'choices_as_values' => true,
            ]);
        } elseif ($subject instanceof HighlightedProject) {
            $formMapper->add('project', 'sonata_type_model', [
                'label' => 'global.project',
                'class' => Project::class,
                'choices_as_values' => true,
            ]);
        } elseif ($subject instanceof HighlightedEvent) {
            $formMapper->add('event', 'sonata_type_model', [
                'label' => 'admin.fields.highlighted_content.event',
                'class' => Event::class,
                'choices_as_values' => true,
            ]);
        } elseif ($subject instanceof HighlightedTheme) {
            $formMapper->add('theme', 'sonata_type_model', [
                'label' => 'global.theme',
                'class' => Theme::class,
                'choices_as_values' => true,
            ]);
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('down', $this->getRouterIdParameter().'/down');
        $collection->add('up', $this->getRouterIdParameter().'/up');
    }
}
