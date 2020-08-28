<?php

use Commune\Chatbot\Hyperf\Config\Shells;
use Commune\Chatbot\Hyperf\Config\HfHostConfig;
use Commune\Chatbot\Hyperf\Config\Platforms\StdioConsolePlatformConfig;
use Commune\Chatbot\Hyperf\Config\Platforms\StdioShellPlatformConfig;
use Commune\Chatbot\Hyperf\Config\Platforms\TcpGhostPlatformConfig;
use Commune\Chatlog\ChatlogSocketIOPlatformConfig;
use Commune\Platform\Wechat\WechatPlatformConfig;


/**
 * Host 的配置.
 */
return new HfHostConfig([
    'id' => 'commune_studio',
    'name' => 'commune_studio',

    'ghost' => new \Commune\Chatbot\Hyperf\Config\HfGhostConfig([
        'id' => 'commune',
        'name' => 'commune chatbot',
    ]),


    'shells' => [

        /**
         * 本地 Console 的 Shell 端.
         */
        new Shells\HfShellConfig([
            'id' => 'console',
            'name' => 'console',
        ]),

        // chatlog shell
        new Shells\HfShellConfig([
            'id' => 'chatlog',
            'name' => 'chatlog shell',
        ]),

        // wechat shell
        new Shells\HfShellConfig([
            'id' => 'wechat',
            'name' => 'wechat shell',
        ]),
    ],

    'platforms' => [

        new TcpGhostPlatformConfig([
            'id' => 'tcp_ghost',
            'name' => 'ghost服务端',
            'desc' => '基于 Swoole 协程实现的 Ghost Tcp 服务端. 使用 Babel 类传输协议.',
            'bootShell' => null,
            'bootGhost' => true,
        ]),

        new StdioConsolePlatformConfig([
            'id' => 'stdio_console',

            'name' => 'stdio 本地端',
            'desc' => '使用 Clue\React\Stdio 实现的本地机器人',

            'bootShell' => 'console',
            'bootGhost' => true,

        ]),

        new StdioShellPlatformConfig([
            'id' => 'stdio_shell',

            'name' => 'stdio shell',
            'desc' => '使用 Clue\React\Stdio 实现的本地 Shell',

            'bootShell' => 'console',

        ]),

        // socket io
        new ChatlogSocketIOPlatformConfig([
            'id' => 'chatlog_socketio',
            'name' => 'chatlog socket.io 平台',
            'desc' => '基于 Socket.io 启动的 Websocket 平台, 与前端对接',
            'bootShell' => 'chatlog',
        ]),

        new WechatPlatformConfig([
            'id' => 'wechat',
            'name' => 'wechat official account server',
            'desc' => '基于 EasyWechat 的微信公众号的服务端',
            'bootShell' => 'wechat',
        ])
    ],
]);
