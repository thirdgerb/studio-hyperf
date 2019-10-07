<?php

/**
 * Class SelectIntentTemp
 * @package Commune\Platform\DuerOS\Templates\Dialog
 */

namespace Commune\Platform\DuerOS\Templates\Dialog;


use Commune\Chatbot\App\Messages\QA\Contextual\ChooseIntent;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Platform\DuerOS\Messages\Directives\SelectIntentDirective;
use Commune\Platform\DuerOS\Templates\QuestionTemp;

class SelectIntentTemp extends QuestionTemp
{

    protected function renderDirective(Question $question, Conversation $conversation, array $suggestions): array
    {
        if (!$question instanceof ChooseIntent) {
            return parent::renderDirective($question, $conversation, $suggestions);
        }

        $intents = $question->getIntents();
        if (empty($intents) || empty($suggestions)) {
            return [];
        }

        return [new SelectIntentDirective($suggestions, $intents)];
    }
}