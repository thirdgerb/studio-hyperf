<?php

/**
 * Class ConfirmTemp
 * @package Commune\DuerOS\Templates\Dialog
 */

namespace Commune\DuerOS\Templates\Dialog;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\DuerOS\Templates\QuestionTemp;

class ConfirmTemp extends QuestionTemp
{
    protected function renderDirective(Question $question, Conversation $conversation): array
    {
        // confirm 的场景中, 不需要匹配序号.
        return [];
    }
}