<?php

use Commune\Hyperf\Commands\Tinker;

return [

    Tinker::CONFIG_KEY => include COMMUNE_PATH . '/configs/chatbots/tinker.php',

    'apps' => [
        'tcp' => include COMMUNE_PATH . '/configs/tcp.php',
        'dueros' => include COMMUNE_PATH . '/configs/dueros.php',
    ]

];