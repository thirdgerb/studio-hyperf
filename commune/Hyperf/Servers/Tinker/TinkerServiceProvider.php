<?php

/**
 * Class WorkerServiceProvider
 * @package Commune\Hyperf\Servers\Tinker
 */

namespace Commune\Hyperf\Servers\Tinker;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;

class TinkerServiceProvider extends BaseServiceProvider
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
        $this->app->singleton(Supervise::class, function(){

            // 用匿名类简单实现一个ability
            return new class implements Supervise {

                public function isAllowing(Conversation $conversation): bool
                {
                    return true;
                }
            };
        });

    }


}