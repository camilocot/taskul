<?php

namespace Taskul\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Taskul\TaskBundle\DBAL\Types\TaskStatusType;

class TagType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('id')
        ->add('name')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Taskul\TaskBundle\Entity\Tag',
            'csrf_protection'   => false,
            ));
    }

    public function getName()
    {
        return '';
    }
}
