<?php


namespace App\Validator\Constraints;

use App\Entity\Proposition;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\PropositionRepository;
use App\Repository\QuizRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DisableQuizFinalizedValidator extends ConstraintValidator
{
    /**
     * @var QuizRepository
     */
    private $quizRepository;
    /**
     * @var PropositionRepository
     */
    private $propositionRepository;

    /**
     * DisableQuizFinalizedValidator constructor.
     * @param QuizRepository $quizRepository
     * @param PropositionRepository $propositionRepository
     */
    public function __construct(QuizRepository $quizRepository, PropositionRepository $propositionRepository)
    {
        $this->quizRepository = $quizRepository;
        $this->propositionRepository = $propositionRepository;
    }

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

        /* Avoid quiz modification if quiz is already 'finalized' */
        if ($value instanceof Quiz) {
            if (!empty($value->getId())) {
                $isFinalized = $this->quizRepository->isFinalized($value->getId());
                if ($isFinalized) {
                    $this->context->buildViolation($constraint->message)->addViolation();
                }
            }

        /* Avoid question modification if quiz is already 'finalized' */
        } elseif ($value instanceof Question && $value->getquiz()->getStatus() === 'finalized') {
            $this->context->buildViolation($constraint->message)->addViolation();

        /* Avoid proposition modification if quiz is already 'finalized' */
        } elseif ($value instanceof Proposition && $value->getQuestion()->getquiz()->getStatus() === 'finalized') {
            // We check the fields one by one to permit modification on th relation proposition-answer
            $existingProp = $this->propositionRepository->findOneBy(['id' => $value->getId()]);
            if (!empty($existingProp)) {
                if ($existingProp->getLabel() !== $value->getLabel() ||
                    $existingProp->getWrightAnswer() !== $value->getWrightAnswer() ||
                    $existingProp->getPosition() !== $value->getPosition())

                    $this->context->buildViolation($constraint->message)->addViolation();
            }
        }

    }
}
