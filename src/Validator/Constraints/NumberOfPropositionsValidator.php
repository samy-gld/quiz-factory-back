<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NumberOfPropositionsValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NumberOfPropositions) {
            throw new UnexpectedTypeException($constraint, NumberOfPropositions::class);
        }

        $typeTab = [
            0 => ['duo', 2],
            1 => ['carre', 4],
            2 => ['qcm', 6]
        ];
        $question = $value;
        $type = $question->getType();
        foreach ($typeTab as $acceptedType) {
            if ($acceptedType[0] === $type) {
                $checkNbProps = $acceptedType[1];
                break;
            }
        }

        if (isset($checkNbProps) && $checkNbProps !== count($question->getPropositions())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ type }}', $type)
                ->setParameter('{{ number }}', $checkNbProps)
                ->addViolation();
        }
    }
}
