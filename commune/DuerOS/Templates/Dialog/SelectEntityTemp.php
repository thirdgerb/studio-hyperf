<?php

/**
 * Class SelectEntityTemp
 * @package Commune\DuerOS\Templates\Dialog
 */

namespace Commune\DuerOS\Templates\Dialog;


use Commune\Chatbot\App\Messages\QA\Contextual\ChooseEntity;
use Commune\Chatbot\App\Messages\QA\Contextual\ContextualQ;
use Commune\Chatbot\App\Messages\QA\Contextual\SelectEntity;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\DuerOS\Messages\Directives\SelectSlotDirective;
use Commune\DuerOS\Templates\QuestionTemp;

class SelectEntityTemp extends QuestionTemp
{
    protected function expectSlot(Question $question): ? string
    {
        return $question instanceof ContextualQ
            ? $question->getEntityName()
            : null;
    }

    protected function renderDirective(Question $question, Conversation $conversation): array
    {
        if (
            !$question instanceof SelectEntity
            && !$question instanceof ChooseEntity
        ) {
            return parent::renderDirective($question, $conversation);
        }

        $suggestions = $question->getSuggestions();
        if (empty($suggestions)) {
            return [];
        }

        $intent = $question->getIntent();
        $name = $question->getIntentName();
        $entityName = $question->getEntityName();
        $nlu = $this->getDuerRequest($conversation)->getDuerRequest()->getNlu();

        if (
            $name === $nlu->getIntentName()
            && isset($intent)
            && isset($entityName)
        ) {
            return [ new SelectSlotDirective($intent, $entityName, $suggestions)];
        }

        return [];
    }

}