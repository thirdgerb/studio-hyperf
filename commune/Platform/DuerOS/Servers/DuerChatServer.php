<?php

/**
 * Class DuerOSServer
 * @package Commune\Platform\DuerOS\Servers
 */

namespace Commune\Platform\DuerOS\Servers;


use Commune\Chatbot\Blueprint\Application;
use Commune\Platform\DuerOS\DuerOSComponent;
use Commune\Hyperf\Foundations\Drivers\StdConsoleLogger;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Hyperf\Contract\OnRequestInterface;
use Hyperf\Server\ServerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

class DuerChatServer implements OnRequestInterface
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
     * @var DuerOSComponent
     */
    protected $duerOSOption;

    /**
     * @var \Swoole\Server
     */
    protected $swooleServer;

    /**
     * @var string
     */
    protected $privateKeyContent;


    /**
     * DuerOSServer constructor.
     * @param StdConsoleLogger $logger
     */
    public function __construct(ContainerInterface $container, Application $chatApp, AppServerOption $botOption)
    {
        $this->chatApp = $chatApp;
        $this->botOption = $botOption;
        $this->container = $container;
        $this->swooleServer = $this->container
            ->get(ServerFactory::class)
            ->getServer()
            ->getServer();

        $this->duerOSOption =$this->chatApp->getProcessContainer()->get(DuerOSComponent::class);
    }

    protected function getPrivateKeyContent() : string
    {
        if (isset($this->privateKeyContent)) {
            return $this->privateKeyContent;
        }

        $keyFile = $this->duerOSOption->privateKey;

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
        $chatbotRequest = $this->generateRequest($request, $response);

        try {
            $this->chatApp->getKernel()->onUserMessage($chatbotRequest);
            $response->end();

        } catch (\Throwable $e) {
            $this->chatApp->getConsoleLogger()->critical((string) $e);
            $response->status(500);
            $response->end('failure');
        }
    }


    protected function generateRequest(SwooleRequest $request, SwooleResponse $response) : DuerChatRequest
    {
        $mock = DuerChatRequest::getMockingQuery($request);
        if ($this->botOption->chatbot->debug && !empty($mock)) {
            return $this->makeMockDuerChatRequest($request, $response, $mock);
        }

        $privateKeyContent = $this->getPrivateKeyContent();
        return new DuerChatRequest(
            $this->botOption,
            $this->duerOSOption,
            $this->swooleServer,
            $this->chatApp->getProcessContainer()[LoggerInterface::class],
            $request,
            $response,
            DuerChatRequest::fetchRawInputOfRequest($request),
            $privateKeyContent
        );
    }


    protected function makeMockDuerChatRequest(SwooleRequest $request, SwooleResponse $response, string $mock) : DuerChatRequest
    {
        $rawInput = DuerChatRequest::fetchRawInputOfRequest($request);

        $requestData = [];
        if (!empty($rawInput)) {
            $requestData = json_decode($rawInput, true) ?? [];
        }

        $requestData = empty($requestData)
            ? $this->duerOSOption->requestStub
            : array_replace_recursive($this->duerOSOption->requestStub, $requestData);

        // 敏感数据脱敏. 避免有知道mock可用的做坏事.
        $requestData['session']['sessionId'] = 'test-session-id';
        $requestData['context']['System']['user']['userId'] = 'test-user-id';
        $requestData['context']['System']['user']['userInfo'] = [];
        $requestData['request']['query']['original'] = $mock;
        $requestData['request']['requestId'] = DuerChatRequest::generateUuid();
        $requestData['request']['timestamp'] = strval(time());

        return new DuerChatRequest(
            $this->botOption,
            $this->duerOSOption,
            $this->swooleServer,
            $this->chatApp->getProcessContainer()[LoggerInterface::class],
            $request,
            $response,
            json_encode($requestData),
            ''
        );

    }

}