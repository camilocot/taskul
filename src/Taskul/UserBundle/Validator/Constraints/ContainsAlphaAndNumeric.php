<?php
namespace Taskul\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsAlphaAndNumeric extends Constraint
{
    public $message = 'user.password.alphanumeric';
}