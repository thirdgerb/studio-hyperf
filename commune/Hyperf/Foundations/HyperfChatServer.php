<?php

/**
 * Class AbstractHyperfServer
 * @package Commune\Hyperf\Servers
 */

namespace Commune\Hyperf\Foundations;

use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Hyperf\Foundations\Contracts\SwooleHttpMsgReq;
use Commune\Hyperf\Foundations\Contracts\SwooleMsgReq;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Hyperf\Server\ServerInterface as HyperfServer;
use Psr\Container\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Server\ServerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Swoole\Coroutine;

/**
 * chatbot server
 *
 * 在 hyperf 里做 studio, server 的概念就比较多了:
 *
 * - swoole server : swoole 的服务端
 * - hyperf server : hyperf 对 swoole server 的封装
 * - chat server : commune chatbot 自己要求的 server 对象.
 */
class HyperfChatServer implements ChatServer
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var HyperfServer
     */
    protected $hyperfServer;

    /**
     * HyperfChatServer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function run(): void
    {
        // 查看 ChatAppFactory, 已绑定工厂方法在 config/dependencies.php
        $chatApp = $this->container->get(Application::class);

        // 初始化chatbot
        $chatApp->getProcessContainer()->instance(ChatServer::class, $this);
        $chatApp->bootApp();

        // 运行 hyperf server
        $botOption = $this->container->get(HyperfBotOption::class);

        // 初始化 hyperf server
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $stdLogger = $this->container->get(StdoutLoggerInterface::class);
        $serverFactory = $this->container
            ->get(ServerFactory::class)
            ->setEventDispatcher($dispatcher)
            ->setLogger($stdLogger);


        // 运行hyperf server
        $serverConfig = $botOption->server;
        // 基于server 配置运行.
        if (! $serverConfig) {
            throw new \InvalidArgumentException('At least one server should be defined.');
        }

        $serverFactory->configure($serverConfig);
        $serverFactory->start();
    }

    protected function getHyperfServer() : HyperfServer
    {
        return $this->hyperfServer
            ?? $this->hyperfServer = $this->container
                ->get(ServerFactory::class)
                ->getServer();

    }

    public function sleep(int $millisecond): void
    {
        if (Coroutine::getCid() > 0) {
            Coroutine::sleep($millisecond * 0.001);
        }
    }

    public function fail(): void
    {
        // 关闭当前worker.
        $server = $this->getHyperfServer()->getServer();
        $server->stop($server->worker_id);
    }

    public function closeClient(Conversation $conversation): void
    {
        // 关闭当前连接.
        $request = $conversation->getRequest();
        if ($request instanceof SwooleHttpMsgReq) {
            return;

        } elseif ($request instanceof SwooleMsgReq) {
            $fd = $request->getFd();
            $this->getHyperfServer()->getServer()->close($fd);
        }
    }


}