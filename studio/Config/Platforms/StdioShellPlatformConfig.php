<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2020/7/6
 * Time: 3:32 PM
 */

namespace Commune\HfStudio\Config\Platforms;


use Commune\Framework\Providers\LoggerByMonologProvider;
use Commune\Platform\IPlatformConfig;

class StdioShellPlatformConfig extends IPlatformConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '',
            'concrete' => '',
            'bootShell' => '',
            'bootGhost' => false,
            'providers' => [
                LoggerByMonologProvider::class => [
                    'name' => 'CmuStdioShell',
                    'file' => 'cmu_stdio_shell.log',

                ],
            ],
            'options' => [],
        ];
    }

}