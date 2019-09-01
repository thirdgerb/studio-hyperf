<?php

/**
 * Class NavigationPipe
 * @package Commune\Studio\SessionPipes
 */

namespace Commune\Studio\SessionPipes;

use Commune\Chatbot\App\SessionPipe\NavigationPipe as Example;
use Commune\Chatbot\App\Components\Predefined\Navigation;

/**
 * highest level intent
 */
class NavigationIntentsPipe extends Example
{

    protected $navigationIntents = [
        Navigation\BackwardInt::class,
        Navigation\QuitInt::class,
        Navigation\CancelInt::class,
        Navigation\RepeatInt::class,
        Navigation\RestartInt::class,
    ];

}