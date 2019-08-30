<?php

/**
 * Class DuerOSServer
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;


use Baidu\Duer\Botsdk\Request;
use Baidu\Duer\Botsdk\Response;
use Commune\Hyperf\Foundations\Dependencies\StdConsoleLogger;
use Hyperf\Contract\OnRequestInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Hyperf\HttpMessage\Server\Request as Psr7Request;

class DuerOSServer implements OnRequestInterface
{
    /**
     * @var StdConsoleLogger
     */
    protected $logger;

    /**
     * DuerOSServer constructor.
     * @param StdConsoleLogger $logger
     */
    public function __construct(StdConsoleLogger $logger)
    {
        $this->logger = $logger;
    }


    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        $psr7Request = Psr7Request::loadFromSwooleRequest($request);
        // prepare duer os bot request
        $rawInput = $psr7Request->getBody()->getContents();
        $rawInput = str_replace("", "", $rawInput);
        $postData = json_decode($rawInput, true);
        $botRequest = new Request($postData);


        $botResponse = new Response(
            $botRequest,
            $botRequest->getSession(),
            $botRequest->getNlu()
        );

        $botResponse->setShouldEndSession(false);
        if ($botRequest->getType() === 'IntentRequset') {
            $output = $botResponse->build(['outputSpeech' => '你好']);
            $botResponse->setShouldEndSession(false);
        } else {
            $output = $botResponse->build(['outputSpeech' => '你好啊']);
        }

        $this->logger->info($rawInput);
        $this->logger->info($output);

        $response->end($output);
    }

}