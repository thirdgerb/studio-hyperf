<?php

/**
 * Class MessageQueue
 * @package Commune\Hyperf\Foundations\Contracts
 */

namespace Commune\Hyperf\Foundations\Contracts;


use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;

interface MessageQueue
{
    /**
     * order by created at asc
     * @param string $key
     * @param ConversationMessage[] $messages
     */
    public function push(string $key, array $messages) : void;

    /**
     * order by created at asc
     * @param string $key
     * @return ConversationMessage[]
     */
    public function dump(string $key) : array;

}