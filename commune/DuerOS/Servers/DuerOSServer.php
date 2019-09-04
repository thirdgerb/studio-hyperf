<?php

/**
 * Class DuerOSServer
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;


use Baidu\Duer\Botsdk\Request;
use Baidu\Duer\Botsdk\Response;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\ChatApp;
use Commune\Hyperf\Foundations\Drivers\StdConsoleLogger;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Hyperf\Contract\OnRequestInterface;
use Hyperf\Server\ServerFactory;
use Psr\Container\ContainerInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Hyperf\HttpMessage\Server\Request as Psr7Request;

class DuerOSServer implements OnRequestInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Application
     */
    protected $chatApp;

    /**
     * @var HyperfBotOption
     */
    protected $botOption;

    protected $server;

    /**
     * DuerOSServer constructor.
     * @param StdConsoleLogger $logger
     */
    public function __construct(ContainerInterface $container, Application $chatApp, HyperfBotOption $botOption)
    {
        $this->chatApp = $chatApp;
        $this->botOption = $botOption;
        $this->container = $container;
        $this->server = $this->container
            ->get(ServerFactory::class)
            ->getServer()
            ->getServer();
    }


    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        $chatbotRequest = new DuerOSRequest(
            $this->botOption,
            $this->server,
            $request,
            $response
        );
        $this->chatApp->getKernel()->onUserMessage($chatbotRequest);
    }

}