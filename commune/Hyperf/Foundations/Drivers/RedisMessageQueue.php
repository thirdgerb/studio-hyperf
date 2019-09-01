<?php

/**
 * Class RedisMessageQueue
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;

use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Commune\Hyperf\Foundations\Contracts\MessageQueue;

class RedisMessageQueue implements MessageQueue
{
    const KEY_PREFIX = 'commune:chatbot:messages:';
    /**
     * @var ClientDriver
     */
    protected $driver;

    /**
     * RedisMessageQueue constructor.
     * @param ClientDriver $driver
     */
    public function __construct(ClientDriver $driver)
    {
        $this->driver = $driver;
    }


    public function push(string $key, array $messages): void
    {
        $redis = $this->driver->getRedis();
        $pipe = $redis->multi(\Redis::PIPELINE);

        $key = self::KEY_PREFIX . $key;

        foreach ($messages as $message) {
            $pipe->rPush($key, serialize($message));
        }
        $pipe->exec();
    }

    public function dump(string $key): array
    {
        $redis = $this->driver->getRedis();
        $list = $redis->lTrim($key, 0, -1);

        if (empty($list)) {
            return [];
        }

        return array_map(function(string $serialized){
            return unserialize($serialized);
        }, $list);
    }


}