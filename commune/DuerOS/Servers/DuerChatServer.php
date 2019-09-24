<?php

/**
 * Class DuerOSServer
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;


use Commune\Chatbot\Blueprint\Application;
use Commune\DuerOS\DuerOSComponent;
use Commune\Hyperf\Foundations\Drivers\StdConsoleLogger;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
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
     * @var HyperfBotOption
     */
    protected $botOption;

    /**
     * @var DuerOSComponent
     */
    protected $duerOSOption;

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

        if (!$this->botOption->debug) {
            $chatbotRequest->getCertificate()->enableVerifyRequestSign();
        }

        if ($chatbotRequest->verify()) {
            $this->handleRequest($chatbotRequest);

        } else {
            $this->chatApp
                ->getProcessContainer()
                ->get(LoggerInterface::class)
                ->warning('request failed certificate', [
                    'debug' => $this->botOption->debug,
                    'servers' => $request->server,
                    'privateKeyExists' => !empty($privateKeyContent),
                ]);

            $chatbotRequest->illegalResponse();
        }
    }

    protected function generateRequest(SwooleRequest $request, SwooleResponse $response) : DuerOSRequest
    {
        $mock = DuerOSRequest::getMockingQuery($request);
        if ($this->botOption->debug && !empty($mock)) {
            return $this->makeMockDuerOSRequest($request, $response, $mock);
        }

        $privateKeyContent = $this->getPrivateKeyContent();
        return new DuerOSRequest(
            $this->botOption,
            $this->duerOSOption,
            $this->server,
            $this->chatApp->getProcessContainer()[LoggerInterface::class],
            $request,
            $response,
            DuerOSRequest::fetchRawInputOfRequest($request),
            $privateKeyContent
        );
    }


    protected function makeMockDuerOSRequest(SwooleRequest $request, SwooleResponse $response, string $mock) : DuerOSRequest
    {
        $rawInput = DuerOSRequest::fetchRawInputOfRequest($request);

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
        $requestData['request']['requestId'] = DuerOSRequest::generateUuid();
        $requestData['request']['timestamp'] = strval(time());

        return new DuerOSRequest(
            $this->botOption,
            $this->duerOSOption,
            $this->server,
            $this->chatApp->getProcessContainer()[LoggerInterface::class],
            $request,
            $response,
            json_encode($requestData),
            ''
        );

    }

    protected function handleRequest(DuerOSRequest $request) : void
    {
        $this->chatApp->getKernel()->onUserMessage($request);
    }
}