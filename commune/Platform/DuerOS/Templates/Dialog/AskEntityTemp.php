<?php

/**
 * Class AskEntityTemp
 * @package Commune\Platform\DuerOS\Templates\Dialog
 */

namespace Commune\Platform\DuerOS\Templates\Dialog;

use Commune\Chatbot\App\Messages\QA\Contextual\AskEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ContextualQ;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Platform\DuerOS\Templates\QuestionTemp;
use Commune\Chatbot\Blueprint\Conversation\Conversation;


class AskEntityTemp extends QuestionTemp
{

    protected function expectSlot(Question $question): ? string
    {
        return $question instanceof ContextualQ
            ? $question->getEntityName()
            : null;
    }

    protected function renderDirective(Question $reply, Conversation $conversation, array $suggestions): array
    {
        if (!$reply instanceof AskEntity) {
            return parent::renderDirective($reply, $conversation, $suggestions);
        }

        $request = $this->getDuerRequest($conversation);

        $intentName = $reply->getIntentName();
        $entityName = $reply->getEntityName();
        $nlu = $request->getDuerRequest()->getNlu();

        // 用官方的 nlu
        if ($nlu->getIntentName() == $intentName) {
            $nlu->ask($entityName);
            return [];
        }

        return parent::renderDirective($reply, $conversation, $suggestions);
    }

}