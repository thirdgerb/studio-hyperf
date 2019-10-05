<?php

/**
 * Class ProcessServiceProvider
 * @package Commune\Studio\Providers
 */

namespace Commune\Studio\Providers;

use Commune\Studio\Exceptions\StudioExceptionHandler;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;

/**
 * register exception handler
 */
class ExceptionHandlerServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(
            ExceptionHandler::class,
            StudioExceptionHandler::class
        );
    }


}