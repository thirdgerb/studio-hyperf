<?php

/**
 * Class HyperfServerOption
 * @package Commune\Hyperf\Options
 */

namespace Commune\Hyperf\Foundations\Options;

use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Support\Option;
use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;
use Hyperf\Framework\Bootstrap;


/**
 * @property-read ChatbotConfig $chatbot ChatbotConfig 配置
 *
 * @property-read bool $bufferMessage
 * 是否在发送消息之前, 先缓存消息到队列里.
 * 这样避免异步消息发送失败导致的语序混乱, 还可以实现延迟发送消息
 * whether buffer sending message to cache.
 *
 * @property-read string $redisPool
 * 从 Hyperf 配置中继承的 redis
 *
 * @property-read string $dbPool
 * 从 Hyperf 配置中继承的 db
 *
 * @property-read array $server
 * hyperf 的 server 的配置
 *
 * @property-read string[] $shares
 * 定义需要从Hyperf框架传递到 chatApp 的单例.
 *
 */
class HyperfBotOption extends Option
{

    protected static $associations = [
        'chatbot' => ChatbotConfig::class,
    ];

    public static function stub(): array
    {
        return [

            'debug' => false,

            'chatbot' => ChatbotConfig::stub(),

            'shares' => [
            ],

            'redisPool' => 'default',

            'dbPool' => 'default',

            'bufferMessage' => true,

            'server' => [
                'mode' => SWOOLE_PROCESS,
                'servers' => [
                    [
                        'name' => 'http',
                        'type' => Server::SERVER_TCP,
                        'host' => 'localhost',
                        'port' => 9501,
                        'sock_type' => SWOOLE_SOCK_TCP,
                        'callbacks' => [
                            SwooleEvent::ON_REQUEST => [\Hyperf\HttpServer\Server::class, 'onRequest'],
                        ],
                    ],
                ],
                'settings' => [
                    'enable_coroutine' => true,
                    'worker_num' => 1,
                    'pid_file' => BASE_PATH . '/runtime/studio_demo.pid',
                    'open_tcp_nodelay' => true,
                    'max_coroutine' => 100000,
                    'open_http2_protocol' => true,
                    'max_request' => 100000,
                    'socket_buffer_size' => 2 * 1024 * 1024,
                ],
                'callbacks' => [
                    SwooleEvent::ON_BEFORE_START => [Bootstrap\ServerStartCallback::class, 'beforeStart'],
                    SwooleEvent::ON_WORKER_START => [Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
                    SwooleEvent::ON_PIPE_MESSAGE => [Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
                ],
            ],
        ];
    }


}