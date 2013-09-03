<?php
namespace Taskul\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsAlphaAndNumericValidator extends ConstraintValidator
{
    public function isValid($value, Constraint $constraint)
    {
        if (!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $value, $matches)) {
            $this->setMessage($constraint->message, array('%string%' => $value));

            return false;
        }

        return true;
    }
}