<?php

/**
 * Class StopInt
 * @package Commune\DuerOS\Contexts\Intents\Common
 */

namespace Commune\DuerOS\Contexts\Intents\Common;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\DuerOS\Constants\CommonIntents;

class StopInt extends MessageIntent
{
    const SIGNATURE = 'stop';

    const DESCRIPTION = '停止';

    public static function getContextName(): string
    {
        return CommonIntents::COMMON_STOP;
    }

}