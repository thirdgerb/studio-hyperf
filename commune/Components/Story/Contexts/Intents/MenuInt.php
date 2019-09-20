<?php


namespace Commune\Components\Story\Contexts\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class MenuInt extends MessageIntent
{
    const SIGNATURE = 'menu';
    const DESCRIPTION = '回到菜单';

    const NLU_EXAMPLES = [
        '菜单'
    ];

}