<?php

namespace Taskul\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Taskul\TaskBundle\DBAL\Types\TaskStatusType;

class TaskType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name')
        ->add('id')
        ->add('description','purified_textarea',array('required' => FALSE))
        ->add('dateEnd','date', array('widget'=>'single_text', 'format' => 'dd/MM/yyyy','required'=>FALSE))
        ->add('status', 'status', array('expanded' => TRUE, 'choices' => TaskStatusType::getChoices()))
        // ->add('percent', 'integer')
        // ->add('tags','text', array('mapped'=>FALSE,'data'=>$options['tags'],'required'=>FALSE))
        ->add('members', 'entity', array(
            'class'         => 'Taskul\UserBundle\Entity\User',
            'property' => 'email',
            'multiple'      => TRUE,
            'required'      => FALSE
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Taskul\TaskBundle\Entity\Task',
            'tags' => '',
            'csrf_protection'   => false,
            ));
    }

    public function getName()
    {
        return '';
    }
}
