<?php

/**
 * Class SwooleRequest
 * @package Commune\Hyperf\Foundations\Requests
 */

namespace Commune\Hyperf\Foundations\Contracts;

use Swoole\Server as SwooleServer;

interface SwooleMsgReq
{
    public function getServer() : SwooleServer;

    public function getFd() : int;
}