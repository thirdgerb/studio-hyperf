<?php

/**
 * Class TcpMessageRequest
 * @package Commune\Hyperf\Servers\Tcp
 */

namespace Commune\Hyperf\Servers\Tcp;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Conversation\MessageRequestHelper;
use Commune\Chatbot\Framework\Predefined\SimpleConsoleLogger;
use Commune\Hyperf\Foundations\Contracts\SwooleRequest;
use Commune\Support\Uuid\HasIdGenerator;
use Swoole\Server;

class TcpMessageRequest implements MessageRequest, HasIdGenerator, SwooleRequest
{
    use MessageRequestHelper;

    /**
     * @var string|Message
     */
    protected $input;

    /**
     * 客户端连接
     * @var int
     */
    protected $fd;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var array
     */
    protected $clientInfo;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * TcpMessageRequest constructor.
     * @param Server $server
     * @param int $fd
     * @param string|Message $input
     */
    public function __construct(
        Server $server,
        int $fd,
        $input
    )
    {
        $this->input = $input;
        $this->fd = $fd;
        $this->server = $server;
        $this->clientInfo = $this->server->getClientInfo($fd);
    }


    public function getFd() : int
    {
        return $this->fd;
    }

    public function getInput()
    {
        return $this->input;
    }


    public function getPlatformId(): string
    {
        return TcpServer::class;
    }

    protected function makeInputMessage($input): Message
    {
        return new Text(strval($input));
    }


    public function fetchTraceId(): string
    {
        return $this->fetchMessageId();
    }

    protected function getUserIp() : string
    {
        return $this->clientInfo['remote_ip'] ?? null;
    }

    public function fetchUserId(): string
    {
        return $this->userId
            ?? $this->userId = md5($this->getUserIp());
    }

    public function fetchUserName(): string
    {
        return $this->getUserIp();
    }

    public function fetchUserData(): array
    {
        return [];
    }

    public function bufferConversationMessage(ConversationMessage $message): void
    {
        $this->buffer[] = $message;
    }

    public function flushChatMessages(): void
    {
        while ($message = array_shift($this->buffer)) {
            $this->write($message->getMessage());
        }
        $this->buffer = [];
    }

    protected function write(Message $msg) : void
    {
        // 显示一下颜色.
        if ($msg instanceof VerboseMsg) {

            switch ($msg->getLevel()) {
                case VerboseMsg::DEBUG:
                    $style = 'debug';
                    break;
                case VerboseMsg::INFO:
                    $style = 'info';
                    break;
                case VerboseMsg::WARN:
                    $style = 'warning';
                    break;
                default:
                    $style = 'error';
            }

            $this->server->send(
                $this->fd,
                SimpleConsoleLogger::wrapMessage(
                    $style,
                    $msg->getText()
                )
                . PHP_EOL
            );
        } else {
            $this->server->send($this->fd, $msg->getText() . PHP_EOL);
        }

    }

    public function getServer(): Server
    {
        return $this->server;
    }


}