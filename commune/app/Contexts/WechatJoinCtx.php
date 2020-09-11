<?php


namespace Commune\App\Contexts;


use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\Context\ACodeContext;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 如何用微信加入网页版对话
 *
 *
 * @example 如何用微信加入网页版对话
 * @example 怎么用微信来控制
 * @example 怎么用微信语音控制
 */
class WechatJoinCtx extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption();
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {

            return $dialog
                ->send()
                ->info('app.wechat.officialAccount')
                ->info('app.wechat.join', [
                    'session' => $dialog->cloner->getSessionId(),
                ])
                ->over()
                ->fulfill();
        });
    }


}