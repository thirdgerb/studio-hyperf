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
use Commune\DuerOS\Options\DuerOSOption;
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

    /**
     * @var \Swoole\Server
     */
    protected $server;

    /**
     * @var string
     */
    protected $privateKeyContent;

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

    protected function getPrivateKeyContent() : string
    {
        if (isset($this->privateKeyContent)) {
            return $this->privateKeyContent;
        }
        $option =$this->chatApp->getProcessContainer()->get(DuerOSOption::class);
        $keyFile = $option->privateKey;

        if (empty($keyFile)) {
            $this->chatApp->getConsoleLogger()->warning('dueros openssl verify with empty private key');
            return $this->privateKeyContent = '';
        }

        if (!file_exists($keyFile)) {
            $this->chatApp->getConsoleLogger()->error('dueros openssl private key file '.$keyFile .' not exists!');
            return $this->privateKeyContent = '';
        }

        $this->privateKeyContent = file_get_contents($keyFile);
        return $this->privateKeyContent;
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        $privateKeyContent = $this->getPrivateKeyContent();
        $chatbotRequest = new DuerOSRequest(
            $this->botOption,
            $this->server,
            $request,
            $response,
            $privateKeyContent
        );

        if ($chatbotRequest->verify()) {
            $this->chatApp->getKernel()->onUserMessage($chatbotRequest);

        } else {
            $chatbotRequest->illegalResponse();
        }
    }

}