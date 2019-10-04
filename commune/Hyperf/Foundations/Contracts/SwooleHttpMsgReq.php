<?php


namespace Commune\Hyperf\Foundations\Contracts;

use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

interface SwooleHttpMsgReq extends MessageRequest, SwooleMsgReq
{
    public function getSwooleRequest(): SwooleRequest;

    public function getSwooleResponse(): SwooleResponse;


}