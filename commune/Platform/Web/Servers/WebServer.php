<?php


namespace Commune\Platform\Web\Servers;


use Commune\Chatbot\Blueprint\Application;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Hyperf\Contract\OnRequestInterface;
use Hyperf\Server\ServerFactory;
use Psr\Container\ContainerInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

class WebServer implements OnRequestInterface
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
    protected $swooleServer;

    public function __construct(ContainerInterface $container, Application $chatApp, HyperfBotOption $botOption)
    {
        $this->chatApp = $chatApp;
        $this->botOption = $botOption;
        $this->container = $container;
        $this->swooleServer = $this->container
            ->get(ServerFactory::class)
            ->getServer()
            ->getServer();
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        try {
            $webRequest = new WebRequest(
                $this->botOption,
                $this->swooleServer,
                $request,
                $response
            );

            $this->chatApp->getKernel()->onUserMessage($webRequest);
            $response->end();
        } catch (\Throwable $e) {
            $response->status(500);
            $response->end($e->getMessage());
        }
    }


}