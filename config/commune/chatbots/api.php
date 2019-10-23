<?php

use Commune\Studio\Providers;

$chatbot = include __DIR__ . '/demo.php';

$chatbot['chatbotPipes'] = [
    'onUserMessage' => [
        \Commune\Chatbot\App\ChatPipe\MessengerPipe::class,
        // api 不需要锁用户.
        // \Commune\Chatbot\App\ChatPipe\ChattingPipe::class,
        \Commune\Chatbot\OOHost\OOHostPipe::class,
    ],
];

$chatbot['host']['sessionPipes'] = [
    \Commune\Platform\WebApi\SessionPipes\ApiActionMatcher::class,
];

$chatbot['logger'] = [
    'path' => BASE_PATH . '/runtime/commune_api.log',
    'days' => 7,
    'level' => 'debug',
    'bubble' => true,
    'permission' => NULL,
    'locking' => false,
];


return $chatbot;
