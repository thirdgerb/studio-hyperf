<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

return [
    'dependencies' => [
        \Commune\Hyperf\Foundations\Options\HyperfBotOption::class
            => \Commune\Hyperf\Foundations\Factories\HyperfBotOptionFactory::class,

        \Commune\Chatbot\Blueprint\Application::class
            => \Commune\Hyperf\Foundations\ChatAppFactory::class
    ],
];
