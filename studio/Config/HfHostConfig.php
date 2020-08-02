<?php

namespace Commune\HfStudio\Config;

use Commune\Framework;

use Commune\Host\IHostConfig;
use Commune\Blueprint\Configs\HostConfig;
use Commune\Chatbot\Hyperf\Providers as HfProviders;
use Hyperf\Redis\RedisFactory;

/**
 * 对话机器人 Host 的默认配置.
 * 具体配置项请查看 interface:
 *
 * @see HostConfig
 */
class HfHostConfig extends IHostConfig
{

    public static function stub(): array
    {
        return [
            'id' => 'commune_hf',

            'name' => 'commune_hf',

            'providers' => [

                /* config services */

                // 将 Hyperf 容器的单例平移到 Commune

                HfProviders\HyperfDIBridgeServiceProvider::class => [
                    'singletons' => [
                        RedisFactory::class,
                    ],
                ],

                // 配置中心
                Framework\Providers\OptionRegistryServiceProvider::class,

                // 基于 Hyperf database 的配置仓库
                HfProviders\HfOptionStorageServiceProvider::class,

                /* process services */

                // 基于 Hyperf redis 实现的 cache
                HfProviders\HfCacheServiceProvider::class => [
                    'redis' => 'default',
                ],

                // 消息保存
                HfProviders\HfMessageDBServiceProvider::class => [
                ],

                // 文件缓存. 不一定用到.
                Framework\Providers\FileCacheServiceProvider::class,

                // i18n 多语言模块
                HfProviders\HfTranslatorServiceProvider::class,

                // 注册 mind set 的配置缓存.
                HfProviders\HfMindsetStorageServiceProvider::class,


                // 异常上报模块.
                Framework\Providers\ExpReporterByConsoleProvider::class,

                // sound like 模块, 用于拼音检查.
                Framework\Providers\SoundLikeServiceProvider::class,

                // messenger
                Framework\Providers\ShlMessengerBySwlCoTcpProvider::class => [
                    'ghostHost' => env('TCP_GHOST_HOST', '127.0.0.1'),
                    'ghostPort' => env('TCP_GHOST_PORT', '12315'),
                    'connectTimeout' => 0.3,
                    'receiveTimeout' => 0.3,
                ],

                // 广播模块
                HfProviders\HfBroadcasterServiceProvider::class => [

                ],

                Framework\Providers\LoggerByMonologProvider::class => [
                    'name' => 'commune',
                ],

                /* req services */

            ],

            // 配置单例.
            'options' => [

            ],

            'ghost' => [],

            'shells' => [],

            'platforms' => [],
        ];
    }

}