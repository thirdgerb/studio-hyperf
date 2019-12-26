<?php


namespace Commune\Studio\Listeners;


use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Framework\Events\FinishingRequest;

class LogMessages
{
    public function log(FinishingRequest $e)
    {
        $incomingMessage = $e->conversation
            ->getIncomingMessage()
            ->getMessage()
            ->getText();

        $replies = array_map(function(ConversationMessage $message){
            return $message->getMessage()->getText();
        }, $e->conversation->getReplies());

        $e->conversation
            ->getLogger()
            ->info(__METHOD__, [
                'query' => $incomingMessage,
                'replies' => $replies
            ]);

    }

}