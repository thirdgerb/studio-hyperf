<?php

namespace Commune\Hyperf\Demo\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Cases\Maze\MazeInt;
use Commune\Components\Predefined\Memories\UserInfoMem;

/**
 * @property-read string $name;
 * @property-read string $to
 */
class TestCase extends OOContext
{

    const DESCRIPTION = '测试demo';

    public static function __depend(Depending $depending): void
    {
        $depending->onMemoryVal('name', UserInfoMem::class, 'name');
    }


    public function __exiting(Exiting $listener): void
    {
        // 如果被cancel了就直接退出.
        $listener->onCancel(Redirector::goQuit());
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('你好啊! %name%', ['name' => $this->name])
            ->goStage('menu');

    }

    public function __onMenu(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose('请问我能为您做什么?')
            ->hearing()
                ->todo(function(Dialog $dialog){
                    $dialog->say()->info('你也好啊');
                    return $dialog->wait();
                })
                    ->is('hello')
                    ->is('你好')
                ->todo(function(Dialog $dialog) {
                    $this->to = MazeInt::class;
                    return $dialog->goStage('once');
                })
                    ->isIntent(MazeInt::class)
                    ->is('maze')
                    ->is('迷宫')

                ->todo(function(Dialog $dialog){
                    $dialog->say()->info("您说了退出.");
                    return $dialog->quit();
                })
                    ->is('quit')
                    ->is('退出')
                ->end();
    }

//    public function __onAskName(Stage $stage) : Navigator
//    {
//        return $stage->buildTalk()
//            ->askVerbose('请问我应该如何称呼您?')
//            ->hearing()
//            ->isAnswer(function(Answer $answer, Dialog $dialog){
//                $name = $answer->toResult();
//                if (mb_strlen($name) > 5) {
//                    $dialog->say()->warning('不好意思, 称呼请控制在五个字以内. 换一个称呼可否?');
//                    return $dialog->wait();
//                }
//
//                $info = UserInfoMem::from($this);
//                $info->name = $name;
//
//
//                $dialog->say()->info("您好, %name%!", ['name'=> $info->name ]);
//
//                return $dialog->goStage('menu');
//            })
//            ->end();
//    }

    /**
     * 退出整个机器人.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onOnce(Stage $stage) : Navigator
    {
        return $stage->sleepTo($this->to, Redirector::goQuit());
    }



}