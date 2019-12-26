<?php


namespace Commune\Platform\Web\Servers;


use Commune\Chatbot\Blueprint\Application;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Commune\Platform\Web\WebComponent;
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
     * @var AppServerOption
     */
    protected $botOption;

    /**
     * @var \Swoole\Server
     */
    protected $swooleServer;


    /**
     * @var WebComponent
     */
    protected $config;

    public function __construct(ContainerInterface $container, Application $chatApp, AppServerOption $botOption)
    {
        $this->chatApp = $chatApp;
        $this->botOption = $botOption;
        $this->container = $container;
        $this->swooleServer = $this->container
            ->get(ServerFactory::class)
            ->getServer()
            ->getServer();

        $this->config = $this->chatApp->getProcessContainer()->make(WebComponent::class);
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        try {
            $webRequest = new WebRequest(
                $this->botOption,
                $this->config,
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