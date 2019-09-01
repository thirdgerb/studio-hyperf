<?php

/**
 * Class CacheServiceProvider
 * @package Commune\Hyperf\Foundations\Providers
 */

namespace Commune\Hyperf\Foundations\Providers;


use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Hyperf\Foundations\Drivers\RedisCacheAdapter;

class CacheServiceProvider extends BaseServiceProvider
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
        $this->app->singleton(CacheAdapter::class, RedisCacheAdapter::class);
    }


}