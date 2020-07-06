<?php

namespace Commune\HfStudio\Config\Platforms;

use Commune\Platform\Ghost;
use Commune\Framework\Providers;
use Commune\Platform\IPlatformConfig;

class TcpGhostPlatformConfig extends IPlatformConfig
{
    public static function stub(): array
    {
        return [
            'id' => '',
            'name' => '',
            'desc' => '基于 Swoole 协程实现的 Ghost Tcp 服务端. 使用 Babel 类传输协议.',
            'concrete' => Ghost\Tcp\SwlCoGhostPlatform::class,
            'bootShell' => null,
            'bootGhost' => true,
            'providers' => [
                Providers\GhtMessengerBySwlChanProvider::class => [
                    'chanCapacity' => 1000,
                    'chanTimeout' => 0.1,
                ],

                // 日志
                Providers\LoggerByMonologProvider::class => [
                    'name' => 'CmuGhost',
                    'file' => 'cmu_ghost.log',
                ],
            ],
            'options' => [
                Ghost\Tcp\SwlCoGhostOption::class => [
                    'serverOption' => [
                        'workerNum' => 2,
                        'host' => env('TCP_GHOST_HOST', '127.0.0.1'),
                        'port' => env('TCP_GHOST_PORT', '12315'),
                        // Swoole Server 的配置.
                        'serverSettings' => [
                            'work_num' => 2,
                            'reactor_num' => 2,
                        ],
                    ],
                    /**
                     * @see TcpAdapterOption
                     */
                    'adapterOption' => [
                        'tcpAdapter' => Ghost\Tcp\SwlCoBabelGhostAdapter::class,
                    ],

                ],
            ],
        ];
    }

}