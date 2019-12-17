<?php

$chatbot = include BASE_PATH . '/config/commune/chatbots/tinker.php';

return [

    'chatbot' => $chatbot,

    'redisPool' => 'default',

    'dbPool' => 'default',

    'bufferMessage' => false,

    'shares' => [],

    'server' => [
    ],
];
