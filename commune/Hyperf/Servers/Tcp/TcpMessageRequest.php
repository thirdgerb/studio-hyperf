<?php

/**
 * Class TcpMessageRequest
 * @package Commune\Hyperf\Servers\Tcp
 */

namespace Commune\Hyperf\Servers\Tcp;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Framework\Impl\SimpleConsoleLogger;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Hyperf\Foundations\Requests\AbstractMessageRequest;
use Swoole\Server;

class TcpMessageRequest extends AbstractMessageRequest
{
   /**
     * @var array
     */
    protected $clientInfo;

    /**
     * @var string
     */
    protected $userId;

    /**
     * TcpMessageRequest constructor.
     * @param HyperfBotOption $option
     * @param Server $server
     * @param int $fd
     * @param $input
     */
    public function __construct(
        HyperfBotOption $option,
        $input,
        int $fd,
        Server $server
    )
    {
        $this->clientInfo = $server->getClientInfo($fd);
        parent::__construct($option, $input, $fd, $server);
    }

    protected function doValidate(): bool
    {
        return true;
    }

    protected function illegalResponse(): void
    {
    }

    protected function onBindConversation()
    {
    }


    public function getPlatformId(): string
    {
        return TcpServer::class;
    }

    protected function makeInputMessage($input): Message
    {
        return new Text(strval($input));
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

    /**
     * @param ConversationMessage[] $messages
     */
    protected function renderChatMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->write($message->message);
        }
    }

    protected function flushResponse(): void
    {
    }


    protected function write(Message $msg) : void
    {
        // 显示一下颜色.
        if ($msg instanceof VerbalMsg) {

            switch ($msg->getLevel()) {
                case VerbalMsg::DEBUG:
                    $style = 'debug';
                    break;
                case VerbalMsg::INFO:
                    $style = 'info';
                    break;
                case VerbalMsg::WARN:
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

    public function getScene(): ? string
    {
        return null;
    }

    public function sendRejectResponse(): void
    {
        $this->server->send($this->fd, __METHOD__);
    }

    public function sendFailureResponse(): void
    {
        $this->server->send($this->fd, __METHOD__);
    }


}