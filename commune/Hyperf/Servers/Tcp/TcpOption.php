<?php

/**
 * Class TcpOption
 * @package Commune\Hyperf\Servers\Tcp
 */

namespace Commune\Hyperf\Servers\Tcp;


use Commune\Support\Option;

/**
 * @property-read array $allowIps
 */
class TcpOption extends Option
{
    public static function stub(): array
    {
        return [
            'allowIps' => [
                '127.0.0.1',
            ],
        ];
    }


}