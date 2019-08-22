<?php

/**
 * Class HyperfServerOption
 * @package Commune\Hyperf\Options
 */

namespace Commune\Hyperf\Foundations\Dependencies;

use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Support\Option;
use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;
use Hyperf\Framework\Bootstrap;


/**
 * @property-read ChatbotConfig $chatbot
 * @property-read string $redisPool
 * @property-read string $dbPool
 * @property-read array $server
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

            'chatbot' => ChatbotConfig::stub(),

            'redisPool' => 'default',

            'dbPool' => 'default',

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
                    'pid_file' => BASE_PATH . '/runtime/hyperf_tcp.pid',
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