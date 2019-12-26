<?php

namespace Commune\Platform\DuerOS;

use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Platform\DuerOS\Constants\CommonIntents as DuerIntents;
use Commune\Components\Predefined\Intents\Attitudes;
use Commune\Components\Predefined\Intents\Loop;
use Commune\Platform\DuerOS\Constants\Dictionary;
use Commune\Platform\DuerOS\Providers\RenderServiceProvider;

/**
 * @property-read string $privateKey 私钥的文件路径.
 * @property-read array $intentMapping duerOS 的intent 变成本地的Intent
 * @property-read array[] $entityMapping duerOs 的entity 变成本地的entity
 * @property-read string $rePrompt 用户没有及时响应多轮对话时的回复.
 * @property-read array $requestStub 模拟请求的数据.
 * @property-read string $renderServiceProvider 默认的rendering服务注册
 */
class DuerOSComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [

            'renderServiceProvider' => RenderServiceProvider::class,

            'privateKey' => '',

            // 系统
            'rePrompt' => '没听清, 请再说一次?',

            /**
             * DuerOS intent name 和本地 intent name 的映射关系.
             */
            'intentMapping' => [
                // 默认
                DuerIntents::COMMON_DEFAULT => null,
                // cancel
                DuerIntents::COMMON_CANCEL => Attitudes\DontInt::class,
                // affirm
                DuerIntents::COMMON_CONFIRM => Attitudes\AffirmInt::getContextName(),

                // next
                DuerIntents::COMMON_NEXT => Loop\NextInt::getContextName(),
                // previous
                DuerIntents::COMMON_PREVIOUS => Loop\PreviousInt::getContextName(),

            ],

            'entityMapping' => [
                'dialogue.ordinal' => [
                    Dictionary::SYSTEM_NUMBER => 'ordinal'
                ]

            ],

            // request stub
            'requestStub' => [
                'version' => 'v2.0',
                'session' =>
                    [
                        'new' => true,
                        'sessionId' => 'test-by-mock',
                    ],
                'context' =>
                    [
                        'System' =>
                            [
                                'user' => [
                                    'userId' => 'test-user-id',
                                    'userInfo' => [],
                                ],
                                'application' => [
                                    'applicationId' => 'test-app-id',
                                ],
                            ],

                    ],
                'request' =>
                    [
                        'type' => 'IntentRequest',
                        'requestId' => '',
                        'query' => [
                            'type' => 'TEXT',
                            'original' => '',
                        ],

                        "intents" => [
                            [
                                "name"=> "ai.dueros.common.default_intent",
                                "slots"=> [],
                                "confirmationStatus" => "NONE",
                            ],
                        ],
                        'timestamp' => '0',
                    ],
            ],

        ];
    }


    protected function doBootstrap(): void
    {
        // 注册 render
        $this->app->registerProcessService($this->renderServiceProvider);
    }

}