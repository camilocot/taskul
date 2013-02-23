<?php
namespace Taskul\TaskBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatusType extends AbstractType
{
    /**
     * {@inheritdoc}
     */

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'status';
    }

    private $statusChoices;

    public function __construct(array $statusChoices)
    {
        $this->statusChoices = $statusChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
                $resolver->setDefaults(array(
            'choices' => $this->statusChoices,

        ));
            }

}