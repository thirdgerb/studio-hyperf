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
        $key = self::KEY_PREFIX . $key;

        $serialized = [];
        foreach ($messages as $message) {
            $serialized[] = serialize($message);
        }
        $redis->rPush($key, ...$serialized);
    }

    public function dump(string $key): array
    {
        $redis = $this->driver->getRedis();
        $key = self::KEY_PREFIX . $key;

        $list = $redis->lRange($key, 0, -1);
        $redis->del($key);

        if (empty($list) || is_bool($list)) {
            return [];
        }

        return array_map(function(string $serialized){
            return unserialize($serialized);
        }, $list);
    }


}