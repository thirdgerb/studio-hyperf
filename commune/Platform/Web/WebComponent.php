<?php

namespace Commune\Platform\Web;

use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Platform\Web\Libraries\DemoResponseRender;
use Commune\Platform\Web\Providers\ResponseServiceProvider;
use Commune\Chatbot\App\Messages\QA;
use Commune\Platform\Web\Templates\WebQuestionTemp;
use Commune\Platform\Web\Templates\WebConfirmTemp;

/**
 *
 * @property-read string $apiRender
 * 渲染 api 接口数据的方法. 接口数据返回一个 json
 *
 * 如果要实现更灵活的解耦, 可以自己重新开发一个 web platform
 *
 * @property-read int $maxInputLength 输入数据最大允许长度
 *
 * @property-read array $replyRenders 系统默认的渲w染
 */
class WebComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'apiRender' => DemoResponseRender::class,
            'maxInputLength' => 100,
            'replyRenders' => [

                // base question
                QA\VbQuestion::REPLY_ID => WebQuestionTemp::class,
                QA\Confirm::REPLY_ID => WebConfirmTemp::class,
                QA\Choose::REPLY_ID => WebQuestionTemp::class,
                QA\Selects::REPLY_ID => WebQuestionTemp::class,

                // intent question
                QA\Contextual\AskEntity::REPLY_ID => WebQuestionTemp::class,
                QA\Contextual\ConfirmIntent::REPLY_ID => WebConfirmTemp::class,
                QA\Contextual\ConfirmEntity::REPLY_ID => WebConfirmTemp::class,
                QA\Contextual\ChooseIntent::REPLY_ID => WebQuestionTemp::class,
                QA\Contextual\ChooseEntity::REPLY_ID => WebQuestionTemp::class,
                QA\Contextual\SelectEntity::REPLY_ID => WebQuestionTemp::class,

            ]
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

        $this->registerReplyRender($this->replyRenders);
    }



}