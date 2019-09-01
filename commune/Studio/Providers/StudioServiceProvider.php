<?php

/**
 * Class ProcessServiceProvider
 * @package Commune\Studio\Providers
 */

namespace Commune\Studio\Providers;


use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Hyperf\Foundations\ProcessContainer;
use Commune\Studio\Exceptions\Handler;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * register exception handler
 */
class StudioServiceProvider extends BaseServiceProvider
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
            Handler::class
        );

        $botOption = $this->app->get(HyperfBotOption::class);

        /**
         * 用 hyperf 的logger 覆盖掉
         * @var ContainerInterface $di
         */
        $di = $this->app[ProcessContainer::HYPERF_CONTAINER_ID];
        $logger = $di->get(LoggerFactory::class)->get($botOption->loggerPool);
        $this->app->instance(LoggerInterface::class, $logger);

    }


}