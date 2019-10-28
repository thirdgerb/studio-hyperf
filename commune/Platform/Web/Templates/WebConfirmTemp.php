<?php


namespace Commune\Platform\Web\Templates;


use Commune\Chatbot\App\Messages\Templates\ConfirmTemp;
use Commune\Chatbot\Blueprint\Message\QA\Question;

class WebConfirmTemp extends ConfirmTemp
{

    protected function renderSuggestionStr(Question $question, array $suggestions): string
    {
        return '';
    }
}