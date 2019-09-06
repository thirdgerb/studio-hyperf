<?php

$chatbot = include __DIR__ . '/chatbot.php';

return [
        'chatbotName' => 'commune-tcp',

        'configBindings' => [
            \Commune\Hyperf\Servers\Tcp\TcpOption::class,
        ],
    ] + $chatbot;
