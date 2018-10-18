<?php
namespace Capco\AppBundle\Form;

use Capco\AppBundle\Entity\Questions\SectionQuestion;
use Capco\AppBundle\Form\Type\PurifiedTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionQuestionType extends AbstractType
{
    /**
     * @// TODO: delete `private` and `required` during the refacto.
     * @see https://github.com/cap-collectif/platform/issues/6073 tech task.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', IntegerType::class);
        $builder->add('title', PurifiedTextType::class);
        $builder->add('description', PurifiedTextType::class);
        $builder->add('helpText', PurifiedTextType::class);
        $builder->add('type', IntegerType::class);
        $builder->add('private', CheckboxType::class);
        $builder->add('required', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => SectionQuestion::class,
        ]);
    }
}
