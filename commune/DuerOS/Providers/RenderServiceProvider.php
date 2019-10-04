<?php

/**
 * Class RenderServiceProvider
 * @package Commune\DuerOS\Providers
 */

namespace Commune\DuerOS\Providers;

use Commune\Chatbot\Framework\Providers\ReplyRendererServiceProvider;
use Commune\Chatbot\App\Messages\System\QuitSessionReply;
use Commune\DuerOS\Templates\QuitTemp;
use Commune\Chatbot\App\Messages\QA;
use Commune\DuerOS\Templates;
use Commune\DuerOS\Templates\Dialog;


class RenderServiceProvider extends ReplyRendererServiceProvider
{
    // 默认覆盖原有的配置.
    protected $force = true;

    /**
     * 会覆盖掉系统默认的 renderer
     * @var array
     */
    protected $templates =[
        // event
        QuitSessionReply::REPLY_ID => QuitTemp::class,

        // base question
        QA\VbQuestion::REPLY_ID => Templates\QuestionTemp::class,
        QA\Confirm::REPLY_ID => Dialog\ConfirmTemp::class,
        QA\Choose::REPLY_ID => Templates\QuestionTemp::class,
        QA\Selects::REPLY_ID => Templates\QuestionTemp::class,

        // intent question
        QA\Contextual\AskEntity::REPLY_ID => Dialog\AskEntityTemp::class,
        QA\Contextual\ConfirmIntent::REPLY_ID => Dialog\ConfirmIntentTemp::class,
        QA\Contextual\ConfirmEntity::REPLY_ID => Dialog\ConfirmEntityTemp::class,

        QA\Contextual\ChooseIntent::REPLY_ID => Templates\QuestionTemp::class,
        QA\Contextual\ChooseEntity::REPLY_ID => Dialog\SelectEntityTemp::class,
        QA\Contextual\SelectEntity::REPLY_ID => Dialog\SelectEntityTemp::class,

    ];

    public function register()
    {
    }
}