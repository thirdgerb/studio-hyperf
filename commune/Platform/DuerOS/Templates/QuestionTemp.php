<?php

/**
 * Class QuestionTemp
 * @package Commune\Platform\DuerOS\Templates
 */

namespace Commune\Platform\DuerOS\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Contracts\Translator;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Platform\DuerOS\Messages\Directives\OrdinalDirective;

class QuestionTemp extends AbstractTemp
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * QuestionTemp constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }


    public function doRender(ReplyMsg $reply, Conversation $conversation): array
    {
        if (!$reply instanceof Question) {
            throw new ChatbotLogicException(
                __METHOD__
                . ' only accept QuestionMsg'
            );
        }

        $suggestions = $this->parseSuggestions($reply);
        $this->expectResponse($reply, $conversation, $suggestions);
        $messages = $this->renderDirective($reply, $conversation, $suggestions);
        return array_merge($messages, $this->renderQuestion($reply, $conversation));

    }

    protected function parseSuggestions(Question $question) : array
    {
        return $question->getSuggestions();
    }

    /**
     * duerOS 可以通过 expect response 来增强语音理解能力.
     *
     * @param Question $question
     * @param Conversation $conversation
     * @param array $suggestions
     */
    protected function expectResponse(Question $question, Conversation $conversation, array $suggestions) : void
    {
        if (empty($suggestions)) {
            return;
        }

        $entityName = $this->expectSlot($question);

        $request = $this->getDuerRequest($conversation);
        $response = $request->getDuerResponse();

        foreach ($suggestions as $index => $suggestion) {
            if (is_string($index)) {
                $response->addExpectTextResponse($index);
            }

            if (isset($entityName)) {
                $response->addExpectSlotResponse(strval($suggestion));
            } else {
                $response->addExpectTextResponse(strval($suggestion));
            }

        }
    }

    /**
     * 答案是否是一个entity的值.
     * @param Question $question
     * @return null|string
     */
    protected function expectSlot(Question $question) : ? string
    {
        return null;
    }

    /**
     * 渲染出和dialog 有关的指令.
     *
     * @param Question $question
     * @param Conversation $conversation
     * @param string[] $suggestions
     * @return Message[]
     */
    protected function renderDirective(Question $question, Conversation $conversation, array $suggestions) : array
    {
        if (empty($suggestions)) {
            return [];
        }

        $entityName = $this->expectSlot($question);
        return [new OrdinalDirective($suggestions, $entityName)];
    }

    /**
     * @param Question $reply
     * @param Conversation $conversation
     * @return Message[]
     */
    protected function renderQuestion(Question $reply, Conversation $conversation) : array
    {
        return [ new Text($reply->getQuery())];
    }

}