<?php

namespace Taskul\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ResettingFormType as BaseRegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;

class ResettingFormType extends BaseRegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('new', 'repeated', array(
            'type' => 'password',
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => 'form.new_password'),
            'second_options' => array('label' => 'form.new_password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
            'first_name'  => 'password', // form.userPass.pass1
        	'second_name' => 'password_confirmation', // form.userPass.pass2
        ));
    }

    public function getName()
    {
        return 'taskul_user_resetting';
    }
}
