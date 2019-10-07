<?php

/**
 * Class AskEntityTemp
 * @package Commune\Platform\DuerOS\Templates\Dialog
 */

namespace Commune\Platform\DuerOS\Templates\Dialog;

use Commune\Chatbot\App\Messages\QA\Contextual\ConfirmIntent;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Platform\DuerOS\Templates\QuestionTemp;
use Commune\Chatbot\Blueprint\Conversation\Conversation;


class ConfirmIntentTemp extends QuestionTemp
{

    protected function renderDirective(Question $question, Conversation $conversation): array
    {
        if (!$question instanceof ConfirmIntent) {
            return parent::renderDirective($question, $conversation);
        }
        $request = $this->getDuerRequest($conversation);
        $intentName = $question->getIntentName();
        $nlu = $request->getDuerRequest()->getNlu();
        if ($nlu->getIntentName() == $intentName) {
            $nlu->setConfirmIntent();
        }
        return [];

    }
}