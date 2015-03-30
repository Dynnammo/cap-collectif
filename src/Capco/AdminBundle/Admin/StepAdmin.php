<?php

namespace Capco\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Capco\AppBundle\Entity\Step;

class StepAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'title',
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array(
                'label' => 'admin.fields.step.title',
            ))
            ->add('startAt', null, array(
                'label' => 'admin.fields.step.start_at',
            ))
            ->add('endAt', null, array(
                'label' => 'admin.fields.step.end_at',
            ))
            ->add('position', null, array(
                'label' => 'admin.fields.step.position',
            ))
            ->add('isEnabled', null, array(
                'label' => 'admin.fields.step.is_enabled',
            ))
            ->add('consultation', null, array(
                'label' => 'admin.fields.step.consultation',
            ))
            ->add('type', null, array(
                'label' => 'admin.fields.step.type',
            ))
            ->add('body', null, array(
                'label' => 'admin.fields.step.body',
            ))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array(
                'label' => 'admin.fields.step.title',
            ))
            ->add('startAt', 'date', array(
                'label' => 'admin.fields.step.start_at',
            ))
            ->add('endAt', 'date', array(
                'label' => 'admin.fields.step.end_at',
            ))
            ->add('position', null, array(
                'label' => 'admin.fields.step.position',
            ))
            ->add('isEnabled', null, array(
                'editable' => true,
                'label' => 'admin.fields.step.is_enabled',
            ))
            ->add('consultation', 'sonata_type_model', array(
                'label' => 'admin.fields.step.consultation',
            ))
            ->add('type', null, array(
                'template' => 'CapcoAdminBundle:Step:type_list_field.html.twig',
                'stepTypeLabels' => Step::$stepTypeLabels,
                'label' => 'admin.fields.step.type',
            ))
            ->add('updatedAt', null, array(
                'label' => 'admin.fields.step.updated_at',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'show' => array(),
                    'delete' => array('template' => 'CapcoAdminBundle:Step:list__action_delete.html.twig'),
                ),
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();
        $formMapper
            ->add('title', null, array(
                'label' => 'admin.fields.step.title',
            ))
            ->add('position', null, array(
                'label' => 'admin.fields.step.position',
            ))
        ;
        if ($subject->isOtherStep()) {
            $formMapper
                ->add('startAt', 'sonata_type_datetime_picker', array(
                    'label' => 'admin.fields.step.start_at',
                    'format' => 'dd/MM/yyyy HH:mm',
                    'attr' => array(
                        'data-date-format' => 'DD/MM/YYYY HH:mm',
                    ),
                ))
                ->add('endAt', 'sonata_type_datetime_picker', array(
                    'label' => 'admin.fields.step.end_at',
                    'format' => 'dd/MM/yyyy HH:mm',
                    'attr' => array(
                        'data-date-format' => 'DD/MM/YYYY HH:mm',
                    ),
                    'help' => 'admin.help.step.endAt',
                ))
                ->add('isEnabled', null, array(
                    'label' => 'admin.fields.step.is_enabled',
                    'required' => false,
                ))
                ->add('type', 'choice', array(
                    'required' => true,
                    'choices' => Step::$stepTypeLabelsNoConsultation,
                    'label' => 'admin.fields.step.type',
                ))
                ->add('body', 'ckeditor', array(
                    'config_name' => 'admin_editor',
                    'label' => 'admin.fields.step.body',
                    'required' => false,
                ))
                ->add('consultation', 'sonata_type_model', array(
                    'label' => 'admin.fields.step.consultation',
                ))
            ;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title', null, array(
                'label' => 'admin.fields.step.title',
            ))
            ->add('body', null, array(
                'label' => 'admin.fields.step.body',
            ))
            ->add('startAt', null, array(
                'label' => 'admin.fields.step.start_at',
            ))
            ->add('endAt', null, array(
                'label' => 'admin.fields.step.end_at',
            ))
            ->add('position', null, array(
                'label' => 'admin.fields.step.position',
            ))
            ->add('isEnabled', null, array(
                'label' => 'admin.fields.step.is_enabled',
            ))
            ->add('type', null, array(
                'template' => 'CapcoAdminBundle:Step:type_show_field.html.twig',
                'stepTypeLabels' => Step::$stepTypeLabels,
                'label' => 'admin.fields.step.type',
            ))
            ->add('consultation', null, array(
                'label' => 'admin.fields.step.consultation',
            ))
            ->add('createdAt', null, array(
                'label' => 'admin.fields.step.created_at',
            ))
            ->add('updatedAt', null, array(
                'label' => 'admin.fields.step.updated_at',
            ))
        ;
    }

    public function getBatchActions()
    {
        return array();
    }
}
