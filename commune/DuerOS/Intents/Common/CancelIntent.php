<?php

/**
 * Class CancelInt
 * @package Commune\DuerOS\Intents\Common
 */

namespace Commune\DuerOS\Intents\Common;


use Commune\Chatbot\App\Components\Predefined\Navigation\CancelInt;
use Commune\DuerOS\Constants\DuerOSCommonIntents;

class CancelIntent extends CancelInt
{

    public static function getContextName(): string
    {
        return DuerOSCommonIntents::COMMON_CANCEL;
    }

}