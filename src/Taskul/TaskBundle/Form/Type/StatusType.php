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

    private $preferredChoice;

    public function __construct($preferredChoice)
    {
        $this->preferredChoice = $preferredChoice;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
                $resolver->setDefaults(array(
                'preferred_choices' => array($this->preferredChoice),

        ));
            }

}