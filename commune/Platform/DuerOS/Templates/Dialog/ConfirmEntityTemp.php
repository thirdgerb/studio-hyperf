<?php

/**
 * Class ConfirmEntityTemp
 * @package Commune\Platform\DuerOS\Templates\Dialog
 */

namespace Commune\Platform\DuerOS\Templates\Dialog;


use Commune\Chatbot\App\Messages\QA\Contextual\ConfirmEntity;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Platform\DuerOS\Templates\QuestionTemp;

class ConfirmEntityTemp extends QuestionTemp
{

    protected function renderDirective(Question $question, Conversation $conversation, array $suggestions): array
    {
        if (!$question instanceof ConfirmEntity) {
            return parent::renderDirective($question, $conversation,  $suggestions);
        }
        $request = $this->getDuerRequest($conversation);
        $intentName = $question->getIntentName();
        $entityName = $question->getEntityName();
        $nlu = $request->getDuerRequest()->getNlu();
        if ($nlu->getIntentName() == $intentName) {
            $nlu->setConfirmSlot($entityName);
        }
        return [];

    }


}