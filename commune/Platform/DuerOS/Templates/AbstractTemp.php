<?php

/**
 * Class DefaultTemp
 * @package Commune\Platform\DuerOS\Templates
 */

namespace Commune\Platform\DuerOS\Templates;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Platform\DuerOS\Servers\DuerChatRequest;

abstract class AbstractTemp implements ReplyTemplate
{
    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        $this->runPolicy($reply, $conversation);
        return $this->doRender($reply, $conversation);
    }

    protected function runPolicy(ReplyMsg $reply, Conversation $conversation): void
    {
        $request = $this->getDuerRequest($conversation);
        // 如果是问题, 就期待回答. 否则不期待.
        if ($reply instanceof Question) {
            $response = $request->getDuerResponse();
            $response->setExpectSpeech(true);
        }
    }

    public function getDuerRequest(Conversation $conversation ) : DuerChatRequest
    {
        $request = $conversation->getRequest();
        if (!$request instanceof DuerChatRequest) {

            $type = is_object($request) ? get_class($request) : gettype($request);
            throw new ChatbotLogicException(static::class . " template could only be used with DuerOS request, $type given");
        }

        return $request;
    }

    abstract public function doRender(ReplyMsg $reply, Conversation $conversation): array;

}