<?php
// src/app/Validator/Constraints/AlmostOneQuestion.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
Class NumberOfPropositions extends Constraint {
    public $message = 'A question with type {{ type }} must have {{ number }} propositions';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
