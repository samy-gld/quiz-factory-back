<?php
// src/app/Validator/Constraints/DisableQuizFinalized.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
Class DisableQuizFinalized extends Constraint {
    public $message = 'The quiz has status \'finalized\' and can\'t be modified';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
