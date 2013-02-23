<?php
namespace Taskul\TaskBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Taskul\TaskBundle\DBAL\EnumStatusType;

class StatusType extends AbstractType
{
    public function getDefaultOptions(array $options)
    {
        return array(
            'choices' => EnumStatusType::getReadables()
        );
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'status';
    }
}