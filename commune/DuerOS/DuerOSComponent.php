<?php

/**
 * Class DuerOSComponent
 * @package Commune\DuerOS
 */

namespace Commune\DuerOS;

use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\DuerOS\Constants\CommonIntents as DuerIntents;
use Commune\Chatbot\App\Components\Predefined\Attitudes;
use Commune\Chatbot\App\Components\Predefined\Loop;


/**
 * @property-read string $name 技能的名字
 * @property-read string $privateKey 私钥的文件路径.
 * @property-read array $intentMapping duerOS 的intent 变成本地的Intent
 * @property-read string[] $registerIntents 项目已注册的 intents.
 * @property-read string $rePrompt 用户没有及时响应多轮对话时的回复.
 */
class DuerOSComponent extends ComponentOption
{
    const IDENTITY = 'name';

    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\DuerOS\\Contexts\\",
            __DIR__ . '/Contexts'
        );

    }

    public static function stub(): array
    {
        return [
            // 技能名字.
            'name' => 'dueros',

            'privateKey' => '',

            'rePrompt' => '没听清, 请再说一次?',


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
                // continue
                DuerIntents::COMMON_CONTINUE => Loop\ContinueInt::getContextName(),

            ],

            // 项目
            'registerIntents' => [

            ],
        ];
    }


}