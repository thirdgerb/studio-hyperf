<?php

/**
 * Class AbstractMessageRequest
 * @package Commune\Hyperf\Foundations\Requests
 */

namespace Commune\Hyperf\Foundations\Requests;

use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Conversation\MessageRequestHelper;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Hyperf\Foundations\Contracts\MessageQueue;
use Commune\Hyperf\Foundations\Contracts\SwooleMsgReq;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Support\Uuid\HasIdGenerator;
use Swoole\Server;

/**
 * 在 studio-hyperf 里实现的 message request
 * 除了提供各种默认的方法之外, 还提供了 message queue 机制
 * 会把需要发送的消息先 buffer 到一个队列里, 然后从队列里读取要渲染的数据.
 *
 * 这个队列相当于一个用户的发件箱. 因此也允许别的请求给该用户的发件箱发内容.
 *
 */
abstract class AbstractMessageRequest implements MessageRequest, SwooleMsgReq, HasIdGenerator, RunningSpy
{
    use RunningSpyTrait, MessageRequestHelper {
        getLogContext as protected getHelperLogContext;
    }


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
     * @var bool
     */
    protected $isValid;

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

        $msgId = $this->fetchMessageId();
        static::addRunningTrace($msgId, $msgId);
    }

    /*-------- methods --------*/

    public function getInput()
    {
        return $this->input;
    }

    /**
     * 渲染消息, 但不要输出. 留到 flushResponse 进行真输出.
     * @param ConversationMessage[] $messages
     */
    abstract protected function renderChatMessages(array $messages) : void;

    /**
     * 真正输出消息.
     */
    abstract protected function flushResponse() : void;

    /**
     * 校验请求.
     * @return bool
     */
    abstract protected function doValidate() : bool;

    /**
     * @return bool
     */
    public function validate(): bool
    {
        return $this->isValid
            ?? $this->isValid = $this->doValidate();
    }

    /**
     * 从 conversation 获取要发送的消息.
     * 由于一般不是双通的平台, 所以不直接入队, 而是先 buffer 到内存中.
     *
     * @param ConversationMessage $message
     */
    public function bufferMessage(ConversationMessage $message): void
    {
        $id = $message->getUserId();
        $key = $this->userMessageBufferKey($id);

        // 按发送用户不同, 将消息分散到多个收件箱缓冲里.
        $this->buffer[$key][] = $message;
    }

    /**
     * 用户收件箱在 message queue 中使用的 key
     * @param string $userId
     * @return string
     */
    protected function userMessageBufferKey(string $userId) : string
    {
        $botName = $this->getChatbotName();
        return "$botName:$userId";
    }

    /**
     * 获取 message queue
     * @return MessageQueue
     */
    protected function queue() : MessageQueue
    {
        return $this->queue
            ?? $this->queue = $this->conversation
                ->make(MessageQueue::class);
    }


    /**
     * 发送所有的消息给用户.
     */
    public function sendResponse(): void
    {
        // 按配置决定是否 buffer 消息到 message queue 中
        if ($this->botOption->bufferMessage) {
            $this->sendMessagesToBuffer();
        }

        // 如果请求不合法, 不继续渲染了.
        if (!$this->validate()) {
            return;
        }

        // 读取buffer
        if ($this->botOption->bufferMessage) {
            $buffered = !$this->flushed ? $this->fetchCachedMessages() : [];

        } else {
            $key = $this->userMessageBufferKey($this->fetchUserId());
            $buffered = $this->buffer[$key] ?? [];
        }

        // 渲染 buffer
        if (!empty($buffered)) {
            $this->renderChatMessages($buffered);
        }

        // 输出 response
        if (!$this->flushed) {
            $this->flushResponse();
            $this->flushed = true;
        }
    }

    /**
     * 将已缓冲的消息, buffer 到 message queue
     */
    public function sendMessagesToBuffer() : void
    {
        if (!empty($this->buffer)) {
            // 按收件箱分为多组.
            foreach ($this->buffer as $key => $messages) {
                $this->sendMessageToUserMsgBox($key, $messages);
            }
            // 清空内存缓存.
            $this->buffer = [];
        }
    }

    /**
     * 发送消息给用户的收件箱.
     * @param string $bufferKey
     * @param ConversationMessage[] $messages
     */
    protected function sendMessageToUserMsgBox(string $bufferKey, array $messages) : void
    {
        if (empty($messages)) {
            return;
        }
        $this->queue()->push($bufferKey, $messages);
    }

    /**
     * 从缓存中读取消息.
     * @return ConversationMessage[]
     */
    public function fetchCachedMessages() : array
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
                $rendering[] = $cachedMessage;
            } else {
                $delay[] = $cachedMessage;
            }
        }

        if (!empty($delay)) {
            $this->sendMessageToUserMsgBox($key, $delay);
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

    public function getLogContext(): array
    {
        $context = $this->getHelperLogContext();
        $context['req']['swlFd'] = $this->getFd();
        $server = $this->getServer();
        $context['req']['swlPort'] = $server->port;
        $context['req']['swlWorker'] = $server->worker_id;

        return $context;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->fetchMessageId());
    }

}