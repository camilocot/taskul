<?php
namespace Taskul\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsAlphaAndNumeric extends Constraint
{
    public $message = 'The password should have both letters and numbers.';
}