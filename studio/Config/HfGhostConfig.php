<?php

namespace Commune\HfStudio\Config;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Ghost\IGhostConfig;
use Commune\Components;
use Commune\Kernel\GhostCmd;
use Commune\Ghost\Providers as GhostProviders;
use Commune\Chatbot\Hyperf\Providers as HfProviders;
use Commune\Components\Predefined\Intent\Navigation;
use Commune\Kernel\Handlers\IGhostRequestHandler;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 机器人逻辑内核的配置.
 *
 * @see GhostConfig
 */
class HfGhostConfig extends IGhostConfig
{

    public static function stub(): array
    {
        return [
            'id' => '',

            'name' => '',

            'providers' => [

                /* process service */

                // 注册 mind set 的基本模块
                GhostProviders\MindsetServiceProvider::class,


                /* req service */

                // runtime driver
                HfProviders\HfRuntimeDriverProvider::class,

                // clone service
                GhostProviders\ClonerServiceProvider::class,
            ],

            'options' => [],

            'components' => [
                // 测试用例
                Components\Demo\DemoComponent::class,
            ],

            // request protocals
            'protocals' => [
                [
                    'protocal' => GhostRequest::class,
                    // interface
                    'interface' => GhostRequestHandler::class,
                    // 默认的
                    'default' => IGhostRequestHandler::class,
                ],
            ],

            // 用户命令
            'userCommands' => [
                GhostCmd\GhostHelpCmd::class,
                GhostCmd\User\HelloCmd::class,
                GhostCmd\User\WhoCmd::class,
                GhostCmd\User\QuitCmd::class,
                GhostCmd\User\CancelCmd::class,
                GhostCmd\User\BackCmd::class,
                GhostCmd\User\RepeatCmd::class,
                GhostCmd\User\RestartCmd::class,
            ],

            // 管理员命令
            'superCommands' => [
                GhostCmd\GhostHelpCmd::class,
                GhostCmd\Super\SpyCmd::class,
                GhostCmd\Super\ScopeCmd::class,
                GhostCmd\Super\ProcessCmd::class,
                GhostCmd\Super\IntentCmd::class,
                GhostCmd\Super\RedirectCmd::class,
            ],

            'comprehensionPipes' => [

            ],

            'mindPsr4Registers' => [
            ],

            // session
            'sessionExpire' => 3600,
            'sessionLockerExpire' => 3,
            'maxRedirectTimes' => 255,
            'maxRequestFailTimes' => 3,
            'mindsetCacheExpire' => 600,
            'maxBacktrace' => 3,
            'defaultContextName' => Components\Demo\Contexts\DemoHome::genUcl()->encode(),
            'sceneContextNames' => [
            ],
            'globalContextRoutes' => [
                Navigation\CancelInt::genUcl()->encode(),
                Navigation\RepeatInt::genUcl()->encode(),
                Navigation\QuitInt::genUcl()->encode(),
                Navigation\HomeInt::genUcl()->encode(),
                Navigation\BackwardInt::genUcl()->encode(),
                Navigation\RestartInt::genUcl()->encode(),
            ]
        ];

    }
}

