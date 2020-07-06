<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2020/7/6
 * Time: 3:09 PM
 */

namespace Commune\HfStudio\Config\Platforms;

use Commune\Platform\Libs;
use Commune\Framework\Providers;
use Commune\Platform\IPlatformConfig;
use Commune\Support\Utils\TypeUtils;
use Commune\Platform\Shell\Stdio\StdioConsolePlatform;

class StdioConsolePlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [

            'id' => '',

            'name' => '',
            'desc' => '使用 Clue\React\Stdio 实现的本地机器人',

            'concrete' => StdioConsolePlatform::class,

            'bootShell' => null,
            'bootGhost' => true,

            'options' => [
            ],

            'providers' => [
                // 用数组来做缓存.
                Providers\CacheByArrProvider::class,
                Providers\MessengerFakeByArrProvider::class,
                // 日志
                Providers\LoggerByMonologProvider::class => [
                    'name' => 'CmuStdio',
                    'file' => 'cmu_stdio.log',
                ],
            ],
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields(
                $data,
                ['id', 'name', 'bootShell', 'bootGhost']
            )
            ?? parent::validate($data);
    }
}