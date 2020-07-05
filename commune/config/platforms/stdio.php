<?php

use Commune\Platform;
use Commune\Platform\Libs;
use Commune\Framework\Providers;

return new Platform\Shell\StdioConsolePlatformConfig([

    'id' => 'stdio',

    'name' => 'stdio 平台',
    'desc' => '使用 Clue\React\Stdio 实现的本地机器人',

    'bootShell' => 'console',
    'bootGhost' => true,

    'options' => [
    ],

    'providers' => [
        // 用数组来做缓存.
        Providers\CacheByArrProvider::class,
        Providers\MessengerFakeByArrProvider::class,
        // 日志
        Providers\LoggerByMonologProvider::class => [
            'name' => 'stdio',
            'file' => 'stdio.log',
        ],
    ],
]);