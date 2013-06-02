<?php
namespace Taskul\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends BaseType
{
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('firstname')
            ->add('lastname')
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
        ;
    }


    public function getName()
    {
        return 'taskul_user_profile';
    }
}