<?php

$chatbot = include __DIR__ . '/demo.php';


$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/tcp.log';

return [
    'chatbotName' => 'commune-tcp',
    'configBindings' => [
        \Commune\Hyperf\Servers\Tcp\TcpOption::class,
    ],
] + $chatbot;
