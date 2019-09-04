<?php

/**
 * Class RenderServiceProvider
 * @package Commune\DuerOS\Providers
 */

namespace Commune\DuerOS\Providers;

use Commune\Chatbot\Framework\Providers\ReplyRendererServiceProvider;
use Commune\Chatbot\App\Messages\System\QuitSessionReply;
use Commune\DuerOS\Templates\QuitTemp;

class RenderServiceProvider extends ReplyRendererServiceProvider
{

    protected $templates =[
        QuitSessionReply::REPLY_ID => QuitTemp::class,
    ];

    public function register()
    {
    }
}