<?php

namespace Commune\Studio\Providers;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;

/**
 * register user ability policies
 */
class FakeAbilityServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(Supervise::class, function(){
            return new class implements Supervise {
                public function isAllowing(Conversation $conversation): bool
                {
                    return true;
                }

            };
        });
    }


}