<?php

/**
 * Class ChooseIntentTemp
 * @package Commune\DuerOS\Templates\Dialog
 */

namespace Commune\DuerOS\Templates\Dialog;


use Commune\Chatbot\App\Messages\QA\Contextual\ChooseIntent;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\DuerOS\Templates\QuestionTemp;

class ChooseIntentTemp extends QuestionTemp
{

    protected function renderDirective(Question $question, Conversation $conversation): array
    {
        if (!$question instanceof ChooseIntent) {
            return parent::renderDirective($question, $conversation);
        }

        $suggestions = $question->getSuggestions();
        $intents = $question->getIntents();

    }

}