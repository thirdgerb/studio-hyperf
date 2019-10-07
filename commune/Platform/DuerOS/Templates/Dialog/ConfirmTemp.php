<?php

/**
 * Class ConfirmTemp
 * @package Commune\Platform\DuerOS\Templates\Dialog
 */

namespace Commune\Platform\DuerOS\Templates\Dialog;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Platform\DuerOS\Templates\QuestionTemp;

class ConfirmTemp extends QuestionTemp
{
    protected function renderDirective(Question $question, Conversation $conversation, array $suggestions): array
    {
        // confirm 的场景中, 不需要匹配序号.
        return [];
    }
}