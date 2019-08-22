<?php

/**
 * Class SwooleRequest
 * @package Commune\Hyperf\Foundations\Requests
 */

namespace Commune\Hyperf\Foundations\Requests;


use Swoole\Server;

interface SwooleRequest
{
    public function getServer() : Server;

    public function getFd() : int;
}