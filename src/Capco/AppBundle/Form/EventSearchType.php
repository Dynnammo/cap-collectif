<?php

namespace Capco\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Capco\AppBundle\Repository\ThemeRepository;

class EventSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('term', 'search', array(
                'required' => false,
                'label' => 'idea.searchform.term',
                'translation_domain' => 'CapcoAppBundle',
            ))
            ->add('theme', 'entity', array(
                'required' => false,
                'class' => 'CapcoAppBundle:Theme',
                'property' => 'title',
                'label' => 'idea.searchform.theme',
                'translation_domain' => 'CapcoAppBundle',
                'query_builder' => function(ThemeRepository $tr) {
                    return $tr->createQueryBuilder('t')
                        ->where('t.isEnabled = :enabled')
                        ->setParameter('enabled', true);
                },
                'empty_value' => 'idea.searchform.all_themes',
                'attr' => array('onchange' => 'this.form.submit()')
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'capco_app_event_search';
    }
}
