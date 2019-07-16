<?php
// src/app/Validator/Constraints/AlmostOneQuestion.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
Class AlmostOneQuestion extends Constraint {
    public $message = 'The quiz {{ quiz }} is not valid. It must contain almost one question.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
