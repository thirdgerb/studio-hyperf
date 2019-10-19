<?php


namespace Commune\Platform\Web;


use Commune\Chatbot\App\Messages\ReplyIds;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Platform\Web\Libraries\DemoResponseRender;
use Commune\Platform\Web\Providers\ResponseServiceProvider;
use Commune\Platform\Web\Renderers\LinkTemp;

/**
 *
 * @property-read string $apiRender
 * 渲染 api 接口数据的方法. 接口数据返回一个 json
 *
 * 如果要实现更灵活的解耦, 可以自己重新开发一个 web platform
 *
 * @property-read int $maxInputLength 输入数据最大允许长度
 */
class WebComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'apiRender' => DemoResponseRender::class,
            'maxInputLength' => 100,
        ];
    }

    protected function doBootstrap(): void
    {
        $this->app->registerConversationService(
            new ResponseServiceProvider(
                $this->app->getConversationContainer(),
                $this->apiRender
            )
        );

        $this->registerReplyRender([
            ReplyIds::LINK => LinkTemp::class
        ]);
    }



}