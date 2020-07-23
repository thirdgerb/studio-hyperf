<?php

declare(strict_types=1);

use Commune\Chatbot\Hyperf\Foundation\HostFactory;
use Commune\Chatbot\Hyperf\Foundation\HostConfigFactory;

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    \Commune\Blueprint\Host::class => HostFactory::class,
    \Commune\Blueprint\Configs\HostConfig::class => HostConfigFactory::class,
];
