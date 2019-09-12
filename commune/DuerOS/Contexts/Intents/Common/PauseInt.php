<?php

/**
 * Class PauseInt
 * @package Commune\DuerOS\Contexts\Intents\Control
 */

namespace Commune\DuerOS\Contexts\Intents\Common;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\DuerOS\Constants\CommonIntents;

class PauseInt extends MessageIntent
{
    const SIGNATURE = 'pause';

    const DESCRIPTION = '暂停';

    public static function getContextName(): string
    {
        return CommonIntents::COMMON_STOP;
    }

}