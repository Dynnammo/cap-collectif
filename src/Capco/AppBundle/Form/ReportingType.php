<?php

namespace Capco\AppBundle\Form;

use Capco\AppBundle\Entity\Reporting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReportingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', [
                'choices' => Reporting::$statusesLabels,
                'translation_domain' => 'CapcoAppBundle',
                'label' => 'reporting.form.status',
                'empty_value' => 'reporting.empty_value',
            ])
            ->add('body', 'textarea', [
                'translation_domain' => 'CapcoAppBundle',
                'label' => 'reporting.form.body',
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'capco_app_reporting';
    }
}
