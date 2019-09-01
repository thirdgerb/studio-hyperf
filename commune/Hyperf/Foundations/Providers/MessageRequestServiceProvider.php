<?php

/**
 * Class MessageServiceProvider
 * @package Commune\Hyperf\Foundations\Providers
 */

namespace Commune\Hyperf\Foundations\Providers;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Hyperf\Foundations\Contracts\MessageQueue;
use Commune\Hyperf\Foundations\Drivers\RedisMessageQueue;

class MessageRequestServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(MessageQueue::class, RedisMessageQueue::class);
    }


}