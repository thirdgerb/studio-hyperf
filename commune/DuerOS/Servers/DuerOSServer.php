<?php

/**
 * Class DuerOSServer
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;


use Baidu\Duer\Botsdk\Request;
use Baidu\Duer\Botsdk\Response;
use Hyperf\Contract\OnRequestInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

class DuerOSServer implements OnRequestInterface
{
    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        // prepare duer os bot request
        $rawInput = $request->getData();
        $rawInput = str_replace("", "", $rawInput);
        $postData = json_decode($rawInput, true);
        $botRequest = new Request($postData);


        $botResponse = new Response(
            $botRequest,
            $botRequest->getSession(),
            $botRequest->getNlu()
        );

        $output = $botResponse->build(['outputSpeech' => '你好']);

        $response->end($output);
    }

}