<?php

use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;

$chatbot = include BASE_PATH . '/config/commune/chatbots/api.php';

return [

    'chatbot' => $chatbot,

    'redisPool' => 'default',

    'dbPool' => 'default',

    'bufferMessage' => false,

    'shares' => [],

    'server' => [
        'mode' => SWOOLE_PROCESS,
        'servers' => [
            [
                'name' => 'wechat',
                'type' => Server::SERVER_HTTP,
                'host' => 'localhost',
                'port' => intval(env('CHAT_API_PORT', 9531)),
                'sock_type' => SWOOLE_SOCK_TCP,
                'callbacks' => [
                    SwooleEvent::ON_REQUEST => [\Commune\Platform\WebApi\Servers\ApiServer::class, 'onRequest'],
                ],
            ],
        ],
        'settings' => [
            'enable_coroutine' => true,
            'worker_num' => 1,
            'pid_file' => BASE_PATH . '/runtime/pid/api.pid',
            'open_tcp_nodelay' => true,
            'max_coroutine' => 100000,
            'open_http2_protocol' => true,
            'max_request' => 100000,
            'socket_buffer_size' => 2 * 1024 * 1024,
        ],

        'callbacks' => [
//            SwooleEvent::ON_BEFORE_START => [Bootstrap\ServerStartCallback::class, 'beforeStart'],
//            SwooleEvent::ON_WORKER_START => [Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
//            SwooleEvent::ON_PIPE_MESSAGE => [Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        ],
    ],
];
