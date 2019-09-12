<?php

/**
 * Class TcpChatServer
 * @package Commune\Hyperf\Servers\Tcp
 */

namespace Commune\Hyperf\Servers\Tcp;


use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Swoole\Server;
use Commune\Chatbot\Blueprint\Application as ChatApp;
use Commune\Chatbot\App\Messages\Events\ConnectionEvt;

class TcpServer
{

    protected $starting = '>>>' . PHP_EOL;
    /**
     * @var string
     */
    protected $ending = '<<<' . PHP_EOL;

    /**
     * @var ChatApp
     */
    protected $chatApp;

    /**
     * @var TcpOption
     */
    protected $tcpOption;

    /**
     * @var HyperfBotOption
     */
    protected $botOption;

    /**
     * TcpServer constructor.
     * @param ChatApp $chatApp
     */
    public function __construct(ChatApp $chatApp, HyperfBotOption $option)
    {
        $this->chatApp = $chatApp;
        $this->botOption = $option;
    }


    public function onConnect(Server $server, int $fd) : void
    {
        $clientInfo = $server->getClientInfo($fd);
        $ip = $clientInfo['remote_ip'];
        $tcpOption = $this->getTcpOption();

        if (in_array($ip, $tcpOption->allowIps)) {
            $this->chatApp
                ->getConsoleLogger()
                ->info("connection $fd for $ip open");

            $request = new TcpMessageRequest(
                $this->botOption,
                new ConnectionEvt(),
                $fd,
                $server
            );

            $this->chatApp->getKernel()->onUserMessage($request);
            $server->send($fd, $this->ending);

        } else {
            $this->chatApp
                ->getConsoleLogger()
                ->info("connection $fd for $ip reject");

            $server->send($fd, 'connection rejected' . PHP_EOL);
            $server->close($fd);
        }
    }

    protected function getTcpOption() : TcpOption
    {
        return $this->tcpOption
            ?? $this->tcpOption = $this->chatApp
                ->getProcessContainer()
                ->get(TcpOption::class);
    }

    public function onReceive(Server $server, int $fd, int $reactorId, string $data) : void
    {
        $server->send($fd, $this->starting);
        $request = new TcpMessageRequest(
            $this->botOption,
            $data,
            $fd,
            $server
        );

        $this->chatApp->getKernel()->onUserMessage($request);
        $server->send($fd, $this->ending);
    }

    public function onClose(Server $server, int $fd) : void
    {
        $this->chatApp
            ->getConsoleLogger()
            ->debug("connection $fd close");
    }

}