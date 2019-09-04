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
use Commune\Hyperf\Foundations\Contracts\MessageQueue;
use Commune\Hyperf\Foundations\Contracts\SwooleRequest;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Support\Uuid\HasIdGenerator;
use Swoole\Server;

abstract class AbstractMessageRequest implements MessageRequest, HasIdGenerator, SwooleRequest
{
    use MessageRequestHelper;


    /*------- params -------*/

    /**
     * @var Message|mixed
     */
    protected $input;

    /**
     * @var HyperfBotOption
     */
    protected $botOption;


    /**
     * @var int
     */
    protected $fd;

    /**
     * @var Server
     */
    protected $server;

    /*-------- cached --------*/

    /**
     * @var ConversationMessage[][]
     */
    protected $buffer = [];

    /**
     * @var bool
     */
    protected $flushed = false;


    /**
     * @var MessageQueue
     */
    protected $queue;

    /**
     * AbstractMessageRequest constructor.
     * @param Message|mixed $input
     * @param HyperfBotOption $botOption
     * @param int $fd
     * @param Server $server
     */
    public function __construct(
        HyperfBotOption $botOption,
        $input,
        int $fd,
        Server $server
    )
    {
        $this->input = $input;
        $this->botOption = $botOption;
        $this->fd = $fd;
        $this->server = $server;
    }


    /*-------- methods --------*/

    public function getInput()
    {
        return $this->input;
    }

    /**
     * 渲染消息, 但未输出.
     * @param ConversationMessage[] $messages
     */
    abstract protected function renderChatMessages(array $messages) : void;

    /**
     * 输出消息.
     */
    abstract protected function flushResponse() : void;


    public function bufferConversationMessage(ConversationMessage $message): void
    {
        $id = $message->getUserId();
        $key = $this->userMessageBufferKey($id);
        $this->buffer[$key][] = $message;
    }

    protected function userMessageBufferKey(string $userId) : string
    {
        $botName = $this->getChatbotName();
        return "$botName:$userId";
    }

    protected function queue() : MessageQueue
    {
        return $this->queue
            ?? $this->queue = $this->conversation
                ->make(MessageQueue::class);
    }


    public function flushChatMessages(): void
    {
        // 使用一个queue 做buffer, 在分布式系统中 有一定必要性. 可选.
        if ($this->botOption->bufferMessage) {
            $cached = $this->sendMessagesThroughBuffer();

        } else {

            $key = $this->userMessageBufferKey($this->fetchUserId());
            $cached = $this->buffer[$key] ?? [];
        }

        if (!empty($cached)) {
            $this->renderChatMessages($cached);
        }

        if (!$this->flushed) {
            $this->flushResponse();
            $this->flushed = true;
        }
    }

    public function sendMessagesThroughBuffer() : array
    {
        // 入队
        if (!empty($this->buffer)) {
            foreach ($this->buffer as $key => $messages) {
                $this->bufferToCache($key, $messages);
            }
            $this->buffer = [];
        }

        // 只渲染一次.
        if ($this->flushed) {
            return [];
        }

        // 出队
        return $this->fetchCachedMessages();
    }

    /**
     * @param string $bufferKey
     * @param array $messages
     */
    protected function bufferToCache(string $bufferKey, array $messages) : void
    {
        if (empty($messages)) {
            return;
        }
        $this->queue()->push($bufferKey, $messages);
    }

    /**
     * @return ConversationMessage[]
     */
    protected function fetchCachedMessages() : array
    {
        $key = $this->userMessageBufferKey($this->fetchUserId());
        $list = $this->queue()->dump($key);

        // 需要 render 的消息
        $rendering = [];
        // 延迟发送的消息.
        $delay = [];
        $now = time();

        foreach ($list as $cachedMessage) {

            if (!$cachedMessage instanceof ConversationMessage) {
                continue;
            }

            // 发送时间.
            $deliverAt = $cachedMessage->message->getDeliverAt();

            if (!isset($deliverAt) || $deliverAt->timestamp < $now) {
                array_unshift($rendering, $cachedMessage);
            } else {
                $delay[] = $cachedMessage;
            }
        }

        if (!empty($delay)) {
            $this->bufferToCache($key, $delay);
        }
        return $rendering;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }



}