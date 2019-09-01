<?php

/**
 * Class SwooleRequest
 * @package Commune\Hyperf\Foundations\Requests
 */

namespace Commune\Hyperf\Foundations\Contracts;

use Swoole\Server as SwooleServer;

interface SwooleRequest
{
    public function getServer() : SwooleServer;

    public function getFd() : int;
}