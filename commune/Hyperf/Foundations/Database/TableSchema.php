<?php

/**
 * Class FieldSchema
 * @package Commune\Chatbot\Laravel\Database
 * @author BrightRed
 */

namespace Commune\Hyperf\Foundations\Database;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\OOHost\Session\Session;
use Hyperf\Database\Schema\Blueprint;

class TableSchema
{
    const SESSION_DATA_TABLE = 'chatbot_session_data';
    const SESSION_DATA_ID = 'data_id';
    const SESSION_DATA_TYPE = 'data_type';
    const SESSION_DATA_SERIALIZED = 'serialized';

    public static function serialized(Blueprint $blueprint) : void
    {
        $blueprint->binary(self::SESSION_DATA_SERIALIZED);
    }

    public static function scope(Blueprint $table) : void
    {
        TableSchema::id('message_id', $table);
        TableSchema::id('session_id', $table);
        TableSchema::id('chat_id', $table);
        TableSchema::id('user_id', $table);
        TableSchema::id('platform_id', $table, 100);
        TableSchema::id('chatbot_name', $table, 100);
        TableSchema::id('conversation_id', $table);
    }
    
    public static function scopeIndex(Blueprint $table) : void
    {
        $table->unique('message_id');
        $table->index(['chat_id', 'session_id', ], 'scope_idx');
    }
    
    public static function getScopeFromSession(Session $session) : array
    {
        $scope = $session->scope;
        return [
            'message_id' => $scope->incomingMessageId,
            'session_id' => $scope->sessionId,
            'chat_id' => $scope->chatId,
            'user_id' => $scope->userId,
            'platform_id' => $scope->platformId,
            'chatbot_name' => $scope->chatbotName,
            'conversation_id' => $scope->conversationId,
        ];
    }
    
    public static function getScopeFromConversation(
        Conversation $conversation,
        string $sessionId = ''
    ) : array
    {
        $chat = $conversation->getChat();
        return [
            'message_id' => $conversation->getIncomingMessage()->getId(),
            'session_id' => $sessionId,
            'chat_id' => $chat->getChatId(),
            'user_id' => $chat->getUserId(),
            'platform_id' => $chat->getPlatformId(),
            'chatbot_name' => $chat->getChatbotName(),
            'conversation_id' => $conversation->getConversationId(),
        ];
    }

    public static function id(string $name, Blueprint $table, int $length = 40) : void
    {
        $table->string($name, $length)
            ->comment("id类型 $name 字段")
            ->default('');
    }

}