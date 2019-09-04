<?php

use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;
use Hyperf\Framework\Bootstrap;

$chatbot = include COMMUNE_PATH . '/configs/chatbots/chatbot.php';

return [
    'chatbot' => $chatbot,

    'redisDriver' => 'default',

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
                    SwooleEvent::ON_RECEIVE
                        => [\Commune\Hyperf\Servers\Tcp\TcpServer::class, 'onReceive'],
                    SwooleEvent::ON_CONNECT
                        => [\Commune\Hyperf\Servers\Tcp\TcpServer::class, 'onConnect'],
                    SwooleEvent::ON_CLOSE
                        => [\Commune\Hyperf\Servers\Tcp\TcpServer::class, 'onClose']
                ],
            ],
        ],
        'settings' => [
            'enable_coroutine' => true,
            'worker_num' => 1,
            'pid_file' => BASE_PATH . '/runtime/pid/tcp.pid',
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
