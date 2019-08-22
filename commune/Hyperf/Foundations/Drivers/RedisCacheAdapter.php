<?php

/**
 * Class HyperfCacheAdapter
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;


use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;

class RedisCacheAdapter implements CacheAdapter
{
    use RunningSpyTrait;

    /**
     * @var HyperfDriver
     */
    protected $driver;

    /**
     * HyperfCacheAdapter constructor.
     * @param HyperfDriver $driver
     */
    public function __construct(HyperfDriver $driver)
    {
        $this->driver = $driver;
    }


    public function set(string $key, string $value, int $ttl): bool
    {
        $ttl = $ttl > 0 ? $ttl : 0;
        return $this->driver->getRedis()->set($key, $value, $ttl);
    }

    public function has(string $key): bool
    {
        return $this->driver->getRedis()->exists($key);
    }

    public function get(string $key): ? string
    {
        $value = $this->driver->getRedis()->get($key);
        return is_string($value) ? $value : null;
    }

    public function lock(string $key, int $ttl = null): bool
    {
        $redis = $this->driver->getRedis();
        $bool = $redis->setnx($key, 'true');
        if ($bool && isset($ttl) && $ttl > 0) {
            $redis->expire($key, $ttl);
        }
        return $bool;
    }

    public function unlock(string $key): bool
    {
        return $this->forget($key);
    }

    public function forget(string $key): bool
    {
        return $this->driver->getRedis()->del($key);
    }


}