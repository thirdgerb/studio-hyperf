<?php

/**
 * Class EventServiceProvider
 * @package Commune\Studio\Providers
 */

namespace Commune\Studio\Providers;

use Commune\Chatbot\Framework\Providers\EventServiceProvider as Example;

class EventServiceProvider extends Example
{
    protected $events = [
//        'eventClassName' => [
//            'callable listener',
//            ['className', 'methodName']
//        ],

    ];

    public function register(): void
    {
    }

}