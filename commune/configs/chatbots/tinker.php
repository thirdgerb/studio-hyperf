<?php

$chatbot = include __DIR__ . '/chatbot.php';

return [

    'chatbotName' => 'commune-tinker',

    'configBindings' => [
        \Commune\Hyperf\Servers\Tinker\TinkerOption::class,
    ],
] + $chatbot;
