<?php


namespace Commune\Platform\Wechat\Servers;


use Commune\Chatbot\Blueprint\Application;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Hyperf\Server\ServerFactory;
use Hyperf\Contract\OnRequestInterface;
use Psr\Container\ContainerInterface;
use Swoole\Server as SwooleServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

class OfficialAccountServer implements OnRequestInterface
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
     * @var SwooleServer
     */
    protected $swooleServer;


    public function __construct(ContainerInterface $container, Application $chatApp, AppServerOption $botOption)
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
            $request = new OfficialAccountRequest(
                $this->botOption,
                $this->swooleServer,
                $request,
                $response
            );

            $this->chatApp->getKernel()->onUserMessage($request);
            $response->end();

        } catch (\Throwable $e) {
            $this->chatApp->getConsoleLogger()->critical((string) $e);
            $response->end('failure');
        }
    }

}