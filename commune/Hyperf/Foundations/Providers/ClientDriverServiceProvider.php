<?php

/**
 * Class HyperfDriverServiceProvider
 * @package Commune\Hyperf\Foundations\Providers
 */

namespace Commune\Hyperf\Foundations\Providers;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Hyperf\Foundations\Contracts\ClientDriver;
use Commune\Hyperf\Foundations\Drivers\HyperfClientDriver;
use Commune\Hyperf\Foundations\ProcessContainer;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface;

class ClientDriverServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    public function boot($app)
    {
    }

    public function register()
    {
        /**
         * @var ContainerInterface $hyperfDI
         */
        $hyperfDI = $this->app[ProcessContainer::HYPERF_CONTAINER_ID];
        // db bind
        if (! $this->app->bound(ConnectionResolverInterface::class)) {
            $this->app->instance(
                ConnectionResolverInterface::class,
                $hyperfDI->get(ConnectionResolverInterface::class)
            );
        }


        // redis bind
        if (! $this->app->bound(RedisFactory::class)) {
            $this->app->instance(
                RedisFactory::class,
                $hyperfDI->get(RedisFactory::class)
            );
        }

        $this->app->singleton(ClientDriver::class, HyperfClientDriver::class);

    }


}