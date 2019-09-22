<?php

/**
 * Class HyperfCacheAdapter
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;


use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;

class RedisCacheAdapter implements CacheAdapter
{
    use RunningSpyTrait;

    /**
     * @var ClientDriver
     */
    protected $driver;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * RedisCacheAdapter constructor.
     * @param ClientDriver $driver
     * @param ChatbotConfig $config
     */
    public function __construct(ClientDriver $driver, ChatbotConfig $config)
    {
        $this->driver = $driver;
        $this->traceId = $driver->getTraceId();
        $this->prefix = md5($config->chatbotName).':';
        static::addRunningTrace($this->traceId, $this->traceId);
    }

    protected function parseKey(string $key) : string
    {
        return $this->prefix.$key;
    }

    public function set(string $key, string $value, int $ttl): bool
    {
        $key = $this->parseKey($key);
        $ttl = $ttl > 0 ? $ttl : 0;
        return $this->driver->getRedis()->set($key, $value, $ttl);
    }

    public function has(string $key): bool
    {
        $key = $this->parseKey($key);
        return $this->driver->getRedis()->exists($key);
    }

    public function get(string $key): ? string
    {
        $key = $this->parseKey($key);
        $value = $this->driver->getRedis()->get($key);
        return is_string($value) ? $value : null;
    }

    public function lock(string $key, int $ttl = null): bool
    {
        $key = $this->parseKey($key);
        $redis = $this->driver->getRedis();
        $bool = $redis->setnx($key, 'true');
        if ($bool && isset($ttl) && $ttl > 0) {
            $redis->expire($key, (int) $ttl);
        }
        return $bool;
    }

    public function unlock(string $key): bool
    {
        $key = $this->parseKey($key);
        return $this->forget($key);
    }

    public function forget(string $key): bool
    {
        $key = $this->parseKey($key);
        return $this->driver->getRedis()->del($key);
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }

}