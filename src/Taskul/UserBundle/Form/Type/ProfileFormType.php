<?php
namespace Taskul\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

 class ProfileFormType extends BaseType
{
    private $class = "Taskul\UserBundle\Entity\User";

    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',null, array('label' => 'form.firstname'))
            ->add('lastname',null, array('label' => 'form.lastname'))
            ->add('email', 'email', array('label' => 'form.email'))
        ;
    }


    public function getName()
    {
        return 'taskul_user_profile';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'translation_domain' => 'UserBundle',
            'data_class' => $this->class,
            'intention'  => 'profile',
        ));
    }
}