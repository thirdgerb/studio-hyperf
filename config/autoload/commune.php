<?php

return [

    // 可以通过 commune:start 命令启动的, 真实客户端.
    // 所有配置都是 HyperfBotOption
    'apps' => [
        'tcp' => include BASE_PATH. '/config/commune/apps/tcp.php',
        'dueros-maze' => include BASE_PATH . '/config/commune/apps/dueros-maze.php',
    ],

    // 可用的机器人. 每个chatbotName应该是独立的.
    // apps里多个app, 可以使用同一个chatbot
    // 因为加载了相同配置和服务, 所以相关数据可以互相影响.
    'chatbot' => [

    ],

    // 独立的多轮对话内核.
    // 每一个都是 HostConfig
    'hosts' => [

    ],

];