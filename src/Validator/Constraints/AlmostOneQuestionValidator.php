<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AlmostOneQuestionValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof AlmostOneQuestion) {
            throw new UnexpectedTypeException($constraint, AlmostOneQuestion::class);
        }

        if ($value->status === 'finalized' && count($value->questions) === 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ quiz }}', $value->getName())
                ->addViolation();
        }
    }
}
