<?php

/**
 * Class NavigationPipe
 * @package Commune\Studio\SessionPipes
 */

namespace Commune\Studio\SessionPipes;

use Commune\Chatbot\App\SessionPipe\NavigationPipe as Example;
use Commune\Components\Predefined\Intents\Navigation;

/**
 * highest level intent
 */
class NavigationIntentsPipe extends Example
{

    protected $navigationIntents = [
        Navigation\QuitInt::class,
        Navigation\CancelInt::class,
    ];

}