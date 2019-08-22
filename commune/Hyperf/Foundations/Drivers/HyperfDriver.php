<?php

/**
 * Class HyperfDriver
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;

use Redis;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Commune\Chatbot\Contracts\CacheAdapter;
use Hyperf\Database\ConnectionInterface;

interface HyperfDriver
{

    public function getContainer() : ContainerInterface;

    public function getCacheAdapter() : CacheAdapter;

    public function getDB() : ConnectionInterface;

    public function getRedis() : Redis;

    public function getLogger() : LoggerInterface;
}