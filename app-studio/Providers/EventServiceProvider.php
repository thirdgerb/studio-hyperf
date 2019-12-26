<?php

namespace Commune\Studio\Providers;

use Commune\Chatbot\Framework\Events\FinishingRequest;
use Commune\Chatbot\Framework\Providers\EventServiceProvider as Example;
use Commune\Studio\Listeners\LogMessages;

class EventServiceProvider extends Example
{
    protected $events = [

        // 默认记录消息到日志
        FinishingRequest::class => [
            [LogMessages::class, 'log'],

        ],

    ];

    public function register(): void
    {
    }

}