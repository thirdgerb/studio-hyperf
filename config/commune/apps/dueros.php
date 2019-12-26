<?php

use Hyperf\Server\Server;
use Hyperf\Server\SwooleEvent;
use Hyperf\Framework\Bootstrap;

// 从另一个文件中, 获取机器人的配置数组
$chatbot = include BASE_PATH . '/config/commune/chatbots/dueros.php';

/**
 * 返回机器人配置, 结构定义在 AppServerOption 类
 * @see \Commune\Hyperf\Foundations\Options\AppServerOption
 */
return [

    // 机器人配置
    'chatbot' => $chatbot,

    // redis 使用 hyperf 提供的哪一个 redis 连接池
    // 连接池定义在 BASE_PATH/config/autoload/redis.php
    'redisPool' => 'dueros',

    // db 连接使用 hyperf 提供的哪一个连接池
    // 定义在 BASE_PATH/config/autoload/database.php
    'dbPool' => 'default',

    // 是否缓冲消息. 是的话, 消息通常先写入一个 message queue
    'bufferMessage' => true,

    // 从 Hyperf 容器, 引入到机器人进程级容器的单例.
    'shares' => [],

    // hyperf 的 server 配置.
    'server' => [
        'mode' => SWOOLE_PROCESS,
        // 定义监听端口的 swoole 实例
        'servers' => [
            [
                // 实例名称
                'name' => 'dueros_',
                // 端口所用协议
                'type' => Server::SERVER_HTTP,
                // host 地址, ip
                'host' => 'localhost',
                // 监听端口
                'port' => intval(env('CHAT_DUEROS_PORT', 9529)),
                // socket 类型, tcp, udp 等
                'sock_type' => SWOOLE_SOCK_TCP,
                // 该协议下, 各种回调事件调用的方法
                'callbacks' => [
                    SwooleEvent::ON_REQUEST => [\Commune\Platform\DuerOS\Servers\DuerChatServer::class, 'onRequest'],
                ],
            ]
        ],

        // server 的公共配置
        'settings' => [
            // 是否开启协程
            'enable_coroutine' => true,
            // worker 进程数量
            'worker_num' => 1,
            // pid 文件地址
            'pid_file' => BASE_PATH . '/runtime/pid/dueros.pid',
            'open_tcp_nodelay' => true,
            // worker进程最大协程任务数量
            'max_coroutine' => 100000,
            'open_http2_protocol' => true,
            // 最大响应请求数. 达到了之后, 该 worker 进程会关闭重启.
            // 用于防止内存泄漏
            'max_request' => 100000,
            'socket_buffer_size' => 2 * 1024 * 1024,
        ],

        // 定义各种 swoole 事件的响应逻辑
        'callbacks' => [
            SwooleEvent::ON_BEFORE_START => [Bootstrap\ServerStartCallback::class, 'beforeStart'],
            SwooleEvent::ON_WORKER_START => [Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
            SwooleEvent::ON_PIPE_MESSAGE => [Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        ],
    ],
];
