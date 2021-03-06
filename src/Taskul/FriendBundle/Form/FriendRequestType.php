<?php

namespace Taskul\FriendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FriendRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email','text',array('translation_domain'=>'FriendBundle','label'=>'friendrequest.new.email'))
        ->add('message','purified_textarea',array('translation_domain'=>'FriendBundle','label'=>'friendrequest.new.message','data'=>'friendrequest.email.new.message'))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Taskul\FriendBundle\Entity\FriendRequest'
        ));
    }

    public function getName()
    {
        return 'taskul_friendbundle_friendrequesttype';
    }
}
