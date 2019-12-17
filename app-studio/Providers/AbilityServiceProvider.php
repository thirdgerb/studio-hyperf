<?php

/**
 * Class AbilityServiceProvider
 * @package Commune\Studio\Providers
 */

namespace Commune\Studio\Providers;


use Commune\Studio\Abilities\IsSupervisor;
use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;

/**
 * register user ability policies
 */
class AbilityServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(Supervise::class, IsSupervisor::class);
    }


}