<?php

/**
 * Class Option
 * @package Commune\Hyperf\Servers\Tinker
 */

namespace Commune\Hyperf\Servers\Tinker;

use Commune\Support\Option as CommuneOption;

/**
 * @property-read string $userId;
 * @property-read string $userName;
 * @property-read string $chatbotUserId;
 */
class TinkerOption extends CommuneOption
{
    public static function stub(): array
    {
        return [
            'userId' => 'testUserId',
            'userName' => 'testUserName',
            'chatbotUserId' => 'tinkerUserId',
        ];
    }


}