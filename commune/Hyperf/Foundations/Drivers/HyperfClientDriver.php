<?php

/**
 * Class HyperfDriver
 * @package Commune\Hyperf\Foundations\Drivers
 */

namespace Commune\Hyperf\Foundations\Drivers;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Redis\RedisFactory;
use Hyperf\Redis\RedisProxy;
use Psr\Log\LoggerInterface;
use Redis;

class HyperfClientDriver implements ClientDriver
{
    use RunningSpyTrait;


    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var HyperfBotOption
     */
    protected $botOption;

    /**
     * @var RedisFactory
     */
    protected $redisResolver;

    /**
     * @var ConnectionResolverInterface
     */
    protected $dbResolver;

    /*------ cached -----*/

    protected $db;

    protected $redis;

    /**
     * HyperfDriver constructor.
     * @param Conversation $conversation
     * @param HyperfBotOption $botOption
     * @param RedisFactory $redisResolver
     * @param ConnectionResolverInterface $dbResolver
     */
    public function __construct(
        Conversation $conversation,
        HyperfBotOption $botOption,
        RedisFactory $redisResolver,
        ConnectionResolverInterface $dbResolver
    )
    {
        $this->conversation = $conversation;
        $this->traceId = $conversation->getTraceId();
        $this->botOption = $botOption;
        $this->redisResolver = $redisResolver;
        $this->dbResolver = $dbResolver;

        static::addRunningTrace($this->traceId, $this->traceId);
    }

    public function getLogger(): LoggerInterface
    {
        return $this->conversation->getLogger();
    }


    public function getDB(): ConnectionInterface
    {
        return $this->db
            ?? $this->db = $this->dbResolver
                ->connection($this->botOption->dbPool);
    }

    /**
     * @return \Redis
     */
    public function getRedis() : RedisProxy
    {
        return $this->redis
            ?? $this->redis = $this
                ->redisResolver
                ->get($this->botOption->redisPool);
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }


    public function __destruct()
    {
        static::removeRunningTrace($this->traceId);
    }
}