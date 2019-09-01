<?php

/**
 * Class HyperfDriver
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Contracts;

use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Redis\RedisProxy;
use Psr\Log\LoggerInterface;

interface ClientDriver extends RunningSpy
{
    public function getTraceId() : string;

    public function getDB() : ConnectionInterface;

    /**
     * @return \Redis
     */
    public function getRedis() : RedisProxy;

    public function getLogger() : LoggerInterface;

}