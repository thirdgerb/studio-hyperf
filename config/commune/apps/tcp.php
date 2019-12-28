<?php

use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;
use Hyperf\Framework\Bootstrap;

$chatbot = include BASE_PATH . '/config/commune/chatbots/tcp.php';

return [
    'chatbot' => $chatbot,

    'redisPool' => 'default',

    'dbPool' => 'default',

    'bufferMessage' => true,

    'shares' => [],

    'server' => [
        'mode' => SWOOLE_PROCESS,
        'servers' => [
            [
                'name' => 'tcp',
                'type' => Server::SERVER_BASE,
                'host' => 'localhost',
                'port' => intval(env('CHAT_TCP_PORT', 9527)),
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
//            SwooleEvent::ON_BEFORE_START => [Bootstrap\ServerStartCallback::class, 'beforeStart'],
//            SwooleEvent::ON_WORKER_START => [Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
//            SwooleEvent::ON_PIPE_MESSAGE => [Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        ],
    ],
];
