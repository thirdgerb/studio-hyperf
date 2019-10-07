<?php

/**
 * Class ChooseIntentTemp
 * @package Commune\Platform\DuerOS\Templates\Dialog
 */

namespace Commune\Platform\DuerOS\Templates\Dialog;


use Commune\Chatbot\App\Messages\QA\Contextual\ChooseIntent;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Platform\DuerOS\Templates\QuestionTemp;

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