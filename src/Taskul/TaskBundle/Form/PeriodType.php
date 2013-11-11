<?php

namespace Taskul\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PeriodType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('begin','date', array('widget'=>'single_text', 'format' => 'dd/MM/yyyy','required'=>TRUE))
            ->add('end','date', array('widget'=>'single_text', 'format' => 'dd/MM/yyyy','required'=>TRUE))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Taskul\TaskBundle\Entity\Period'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'taskul_taskbundle_period';
    }
}
