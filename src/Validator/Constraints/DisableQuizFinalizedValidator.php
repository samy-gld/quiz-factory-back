<?php


namespace App\Validator\Constraints;

use App\Entity\Proposition;
use App\Entity\Question;
use App\Entity\Quiz;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DisableQuizFinalizedValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DisableQuizFinalized) {
            throw new UnexpectedTypeException($constraint, DisableQuizFinalized::class);
        }

        if ($value instanceof Quiz && $value->status === 'finalized') {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        } elseif ($value instanceof Question && $value->getquiz()->getStatus() === 'finalized') {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        } elseif ($value instanceof Proposition && $value->getQuestion()->getquiz()->getStatus() === 'finalized') {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}
