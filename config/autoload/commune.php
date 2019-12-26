<?php


return [

    // tinker 机器人的配置
    'tinker' => include BASE_PATH . '/config/commune/apps/tinker.php',

    // 可以通过 commune:start 命令启动的, 真实客户端.
    // 配置内容请查看 Commune\Hyperf\Foundations\Options\AppServerOption
    'apps' => [
        // 默认的tcp端. 通常供测试用.
        'tcp' => include BASE_PATH . '/config/commune/apps/tcp.php',

        // 系统自带的 web 端, 示范如何开发 web 端的对话机器人.
        'web' => include BASE_PATH . '/config/commune/apps/web.php',

        // 系统自带的 api 端, 可以像 mvc 框架那样通过 http api 访问机器人的数据
        'api' => include BASE_PATH . '/config/commune/apps/api.php',

        // 系统自带的 dueros 端, 可以连接小度音箱设备.
        'dueros' => include BASE_PATH . '/config/commune/apps/dueros.php',

        // 系统自带的 微信公众号服务端. 可作为微信公众号的服务.
        'wechat' => include BASE_PATH . '/config/commune/apps/wechat.php',
    ],
];