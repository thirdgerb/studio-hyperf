<?php

use Commune\Chatbot\Hyperf\Config\HfHostConfig;

return new HfHostConfig([

    'id' => 'commune_hf',
    'name' => 'commune_hf',

    'ghost' => include __DIR__ .'/ghost/demo.php',

    'shells' => [
        'console' => include __DIR__ .'/shells/console.php',
    ],

    'platforms' => [
        'stdio' => include __DIR__ . '/platforms/stdio.php',
    ]

]);
