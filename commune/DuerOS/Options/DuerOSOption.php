<?php

/**
 * Class DuerOSOption
 * @package Commune\DuerOS\Options
 */

namespace Commune\DuerOS\Options;


use Commune\Support\Option;

/**
 * @property-read string $privateKey
 */
class DuerOSOption extends Option
{
    public static function stub(): array
    {
        return [
            'privateKey' => env('DUEROS_PRIVATE_KEY', ''),

            'intentMapping' => [

            ],

        ];
    }


}