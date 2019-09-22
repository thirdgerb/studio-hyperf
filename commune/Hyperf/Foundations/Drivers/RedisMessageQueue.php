<?php

/**
 * Class RedisMessageQueue
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;

use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Commune\Hyperf\Foundations\Contracts\MessageQueue;

class RedisMessageQueue implements MessageQueue
{
    const KEY_PREFIX = ':hyperf:messageQueue:';

    /**
     * @var ClientDriver
     */
    protected $driver;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * RedisMessageQueue constructor.
     * @param ClientDriver $driver
     * @param ChatbotConfig $config
     */
    public function __construct(ClientDriver $driver, ChatbotConfig $config)
    {
        $this->driver = $driver;
        $this->prefix = md5($config->chatbotName) . self::KEY_PREFIX;
    }

    public function push(string $key, array $messages): void
    {
        $redis = $this->driver->getRedis();
        $key = $this->prefix . $key;

        $serialized = [];
        foreach ($messages as $message) {
            $serialized[] = serialize($message);
        }
        $redis->rPush($key, ...$serialized);
    }

    public function dump(string $key): array
    {
        $redis = $this->driver->getRedis();
        $key = $this->prefix . $key;

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