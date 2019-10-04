<?php

/**
 * Class HyperfDriverServiceProvider
 * @package Commune\Hyperf\Foundations\Providers
 */

namespace Commune\Hyperf\Foundations\Providers;


use Commune\Chatbot\Contracts\ClientFactory;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Commune\Hyperf\Foundations\Drivers\HyperfClientDriver;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Hyperf\Foundations\Factories\ClientFactoryBridge;

/**
 * hyperf 的协程客户端, 传递到 commune chatbot 的请求级容器内.
 *
 */
class ClientDriverServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    public function boot($app)
    {
    }

    public function register()
    {
        // db, redis 等协程客户端打包.
        $this->app->singleton(ClientDriver::class, HyperfClientDriver::class);

        // guzzle 的协程客户端.
        $this->app->singleton(ClientFactory::class, ClientFactoryBridge::class);
    }


}