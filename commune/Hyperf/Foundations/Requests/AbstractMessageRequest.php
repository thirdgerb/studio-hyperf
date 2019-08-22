<?php

/**
 * Class AbstractMessageRequest
 * @package Commune\Hyperf\Foundations\Requests
 */

namespace Commune\Hyperf\Foundations\Requests;

use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Conversation\MessageRequestHelper;
use Commune\Hyperf\Foundations\Dependencies\HyperfBotOption;
use Commune\Hyperf\Foundations\Drivers\HyperfDriver;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

abstract class AbstractMessageRequest implements MessageRequest, HasIdGenerator
{
    use IdGeneratorHelper, MessageRequestHelper;


    /*------- params -------*/

    /**
     * @var Message|mixed
     */
    protected $input;


    /**
     * @var HyperfDriver
     */
    protected $driver;


    /**
     * @var HyperfBotOption
     */
    protected $botOption;


    /*-------- cached --------*/

    /**
     * @var ConversationMessage[]
     */
    protected $buffer = [];

    /**
     * @var bool
     */
    protected $render = false;

    public function getInput()
    {
        return $this->input;
    }

    /**
     * 如何渲染并输出数据.
     * @param ConversationMessage[] $messages
     */
    abstract protected function renderChatMessages(array $messages) : void;


    public function bufferConversationMessage(ConversationMessage $message): void
    {
        $this->buffer[] = $message;
    }

    public function getPlatformId(): string
    {
        return $this->botOption->platformId;
    }


    protected function userMessageBufferKey(string $userId) : string
    {
        $platformId = $this->getPlatformId();

        return "commune:message:buffer:$platformId:$userId";
    }


    public function flushChatMessages(): void
    {
        // 入队
        if (!empty($this->buffer)) {
            $this->bufferToCache($this->buffer);
            $this->buffer = [];
        }

        // 只渲染一次.
        if ($this->render) {
            return;
        }

        // 出队
        $cached = $this->fetchCachedMessages();
        if (!empty($cached)) {
            $this->render = true;
            $this->renderChatMessages($cached);
        }
    }


    /**
     * @param ConversationMessage[] $messages
     */
    protected function bufferToCache(array $messages) : void
    {
        if (empty($messages)) {
            return;
        }

        $redis = $this->driver->getRedis();

        // 先把消息压到队列里.
        $pipe = $redis->multi(\Redis::PIPELINE);
        foreach ($messages as $message) {
            $key = $this->userMessageBufferKey($message->getUserId());
            $payload = serialize($key);
            $pipe->lpush($key, $payload);
        }
        $pipe->exec();
    }

    /**
     * @return ConversationMessage[]
     */
    protected function fetchCachedMessages() : array
    {
        $key = $this->userMessageBufferKey($this->fetchUserId());
        $redis = $this->driver->getRedis();

        $list = $redis->lRange($key, 0, -1);
        $redis->del($key);

        // 需要 render 的消息
        $rendering = [];
        // 延迟发送的消息.
        $delay = [];
        $now = time();

        foreach ($list as $serialized) {
            /**
             * @var ConversationMessage $unserialized
             */
            $unserialized = unserialize($serialized);
            if (!$unserialized instanceof ConversationMessage) {
                // 一般不会出现这种情况. 除非 conversationMessage 本身不能序列化
                $this->driver
                    ->getLogger()
                    ->warning(__METHOD__ . ' meet deserializable message ' . $serialized);
                continue;
            }

            // 发送时间.
            $deliverAt = $unserialized->message->getDeliverAt();

            if (!isset($deliverAt) || $deliverAt->timestamp < $now) {
                array_unshift($rendering, $unserialized);
            } else {
                $delay[] = $serialized;
            }
        }

        if (!empty($delay)) {
            $this->bufferToCache($delay);
        }
        return $rendering;
    }

}