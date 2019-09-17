<?php


namespace App\Services;

use App\Entity\Answer;

class QuizExecutionService
{
    public function __construct()
    {
    }

    public function isSuccessAnswer(Answer $answer): bool
    {
        $answeredProps = $answer->getPropositions();
        if ($answeredProps->count() === 0) return false;

        $question = $answeredProps->first()->getQuestion();

        $answeredProps = $answeredProps->map(function ($prop) {
            return $prop->getId();
        });

        $wrightProps = $question->getPropositions()
            ->filter(function ($prop) {
                return $prop->getWrightAnswer() === true;
            })
            ->map(function ($prop) {
                return $prop->getId();
            });

        if ($answeredProps->count() !== $wrightProps->count()) return false;

        $wrightProp = $wrightProps->first();
        while ($wrightProp) {
            if (!$answeredProps->contains($wrightProp)) return false;
            $wrightProp = $wrightProps->next();
        }

        return true;
    }
}
