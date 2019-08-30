<?php

use Commune\Hyperf\Commands\Tinker;

return [

    Tinker::CONFIG_KEY => include COMMUNE_PATH . '/configs/tinker.php',

    'apps' => [
        'tcp' => include COMMUNE_PATH . '/configs/apps/tcp.php',
        'dueros' => include COMMUNE_PATH . '/configs/apps/dueros.php',
    ]

];