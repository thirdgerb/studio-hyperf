<?php


namespace Commune\Platform\Web\Templates;


use Commune\Chatbot\App\Messages\Templates\QuestionTemp;
use Commune\Chatbot\Blueprint\Message\QA\Question;

class WebQuestionTemp extends QuestionTemp
{

    protected function renderSuggestionStr(Question $question, array $suggestions): string
    {
        $str = '';
        if (!empty($suggestions)) {
            foreach ($suggestions as $index => $suggestion) {
                if (is_string($index)) {
                    $str .= PHP_EOL . "[$index] $suggestion";
                }
            }
        }
        return $str;
    }

}