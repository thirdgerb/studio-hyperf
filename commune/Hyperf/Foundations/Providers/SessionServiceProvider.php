<?php

/**
 * Class SessionServiceProvider
 * @package Commune\Hyperf\Foundations\Providers
 */

namespace Commune\Hyperf\Foundations\Providers;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Chatbot\OOHost\Session\Driver as SessionDriver;
use Commune\Hyperf\Foundations\Drivers\SessionDriver as SessionDriverImpl;

class SessionServiceProvider extends BaseServiceProvider
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
        $this->app->singleton(SessionDriver::class, SessionDriverImpl::class);
    }


}