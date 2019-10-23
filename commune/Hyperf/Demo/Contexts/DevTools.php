<?php


namespace Commune\Hyperf\Demo\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Contexts\NLUMatcherTask;

/**
 * 介绍一些开发工具.
 */
class DevTools extends TaskDef
{

    const DESCRIPTION = '开发工具';

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __help(Dialog $dialog) : Navigator
    {
        $name = $this->getDef()->getName();
        $class = static::class;
        $dialog->say()->info(<<<EOF
您现在所在的语境名为 $name ，定义语境的 class 名为 $class
EOF
);
        return $dialog->rewind();
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('hyperf-demo.devTools')
            ->goStage('menu');
    }

    public function __onMenu(Stage $stage) : Navigator
    {
        $menu = new Menu(
            'ask.needs',
            [
                '命令行工具' => [$this, 'command'],
                '帮助功能' => [$this, 'knowHelp'],
                '多轮对话管理工具' => 'conversation',
                '前往入口' => function(Dialog $dialog) {
                    return $dialog->redirect->replaceTo(DemoHome::class, Redirect::PROCESS_LEVEL);
                },
                '结束' => Redirector::goQuit(),
            ]
        );

        return $stage->component($menu);

    }

    public function knowHelp(Dialog $dialog) : Navigator
    {
        $dialog->say()->info(<<<EOF
使用 CommuneChatbot 过程中， 随时输入"？" 或者说出寻求帮助相关的话语（如果命中了帮助意图），就会唤起帮助。

帮助是跟随语境走的，语境变化，帮助的内容也可以跟着变化，由开发者决定。

不过本 Demo 因为时间仓促， 大多数内容都没有开发独立的帮助内容，请见谅！ 
EOF
        );

        return $dialog->rewind();

    }

    public function command(Dialog $dialog) : Navigator
    {
        $dialog->say()->info(<<<EOF
CommuneChatbot 内置命令行功能。可以在对话中使用类似 symfony console 的命令行。

而且可以根据用户权限， 区分用户使用的命令行 （#开头）和管理员使用的命令行 （/开头）。您可以按需求开发自己喜欢的命令行工具。

现在输入 #help，可以随时查看您当前可用的命令。
EOF
        );

        return $dialog->rewind();
    }

    public function __onConversation(Stage $stage) : Navigator
    {
        $menu = new Menu(
            'ask.needs',
            [
                NLUMatcherTask::class,
                '返回' => Redirector::goStage('menu'),
            ]
        );

        return $stage->buildTalk()
            ->info(<<<EOF
用 CommuneChatbot 可以快速开发出用 多轮对话 管理多轮对话机器人的工具，这一点会很有趣。

项目自带了一部分重要的管理工具，不过大多数需要超级管理员权限。

本处列举了一个不用超管权限也可以使用的工具。
EOF
)
            ->toStage()
            ->component($menu);
    }



}