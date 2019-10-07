<?php

/**
 * Class HyperfCacheAdapter
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;


use Commune\Chatbot\App\Drivers\Psr16CacheAdapter;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Psr\SimpleCache\CacheInterface;

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
     * @var Psr16CacheAdapter
     */
    protected $psrCache;

    /**
     * RedisCacheAdapter constructor.
     * @param ClientDriver $driver
     * @param ChatbotConfig $config
     */
    public function __construct(ClientDriver $driver, ChatbotConfig $config)
    {
        $this->driver = $driver;
        $this->traceId = $driver->getTraceId();
        // 每个 chatbot 独享一个前缀. 最好在 database 也做出分割.
        $this->prefix = md5($config->chatbotName).':';
        static::addRunningTrace($this->traceId, $this->traceId);
    }

    protected function parseKey(string $key) : string
    {
        return $this->prefix.$key;
    }

    public function set(string $key, string $value, int $ttl = null): bool
    {
        $key = $this->parseKey($key);
        $ttl = isset($ttl) && $ttl > 0 ? $ttl : 0;
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
        return $this->forget($key);
    }

    public function forget(string $key): bool
    {
        $key = $this->parseKey($key);
        return $this->driver->getRedis()->del($key);
    }

    public function getPSR16Cache(): CacheInterface
    {
        return $this->psrCache
            ?? $this->psrCache = new Psr16CacheAdapter($this);
    }

    public function getMultiple(array $keys, $default = null): array
    {
        $keys = array_map(function($key){
            return $this->parseKey(strval($key));
        }, $keys);

        $gets = $this->driver->getRedis()->mget($keys);

        $result = [];
        foreach ($gets as $index => $value) {
            $key = $keys[$index] ?? null;
            if (!isset($key)) {
                continue;
            }

            $result[$key] = $value === false ? $default : $value;
        }
        return $result;
    }

    public function setMultiple(array $values, int $ttl = null): bool
    {
        $data = [];
        $keys = [];
        foreach ($values as $key => $value) {
            $keys[] = $realKey = $this->parseKey(strval($key));
            $data[$realKey] = $value;
        }

        $redis = $this->driver->getRedis();

        $pipe = $redis->multi(\Redis::PIPELINE);

        foreach ($data as $key => $value) {
            $pipe = $pipe->set($key, $value, intval($ttl));
        }

        $pipe->exec();
        return true;
    }

    public function delMultiple(array $keys): bool
    {
        $keys = array_map(function($key){
            return $this->parseKey(strval($key));
        }, $keys);

        return (bool) $this->driver->getRedis()->del(...$keys);
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }



}