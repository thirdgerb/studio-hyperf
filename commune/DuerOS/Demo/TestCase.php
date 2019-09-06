<?php

/**
 * Class TestCase
 * @package Commune\DuerOS\Demo
 */

namespace Commune\DuerOS\Demo;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Cases\Maze\MazeTask;
use Commune\DuerOS\Demo\Memories\UserInfoMem;

/**
 * @property-read string $to
 */
class TestCase extends OOContext
{

    const DESCRIPTION = '测试demo';

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        $userInfo = UserInfoMem::from($this);

        if (isset($userInfo->name)) {
            return $stage->buildTalk()
                ->info('你好啊! %name%', ['name' => $userInfo->name])
                ->goStage('menu');

        } else {

            return $stage->buildTalk()
                ->goStage('askName');
        }


    }

    public function __onMenu(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose('请问我能为您做什么?')
            ->hearing()
            ->is('你好', function(Dialog $dialog){
                $dialog->say()->info('你也好啊');
                return $dialog->wait();
            })
            ->is('迷宫', function(Dialog $dialog) {
                $this->to = MazeTask::class;
                return $dialog->goStage('once');
            })
            ->is('退出', function(Dialog $dialog){
                $dialog->say()->info("您说了退出.");
                return $dialog->quit();
            })
            ->end();
    }

    public function __onAskName(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose('请问我应该如何称呼您?')
            ->hearing()
            ->isAnswer(function(Answer $answer, Dialog $dialog){
                $name = $answer->toResult();
                if (mb_strlen($name) > 5) {
                    $dialog->say()->warning('不好意思, 称呼请控制在五个字以内. 换一个称呼可否?');
                    return $dialog->wait();
                }

                $info = UserInfoMem::from($this);
                $info->name = $name;


                $dialog->say()->info("您好, %name%!", ['name'=> $info->name ]);

                return $dialog->goStage('menu');
            })
            ->end();
    }

    public function __onOnce(Stage $stage) : Navigator
    {
        return $stage->sleepTo($this->to, Redirector::goQuit());
    }

    public function __exiting(Exiting $listener): void
    {
    }


}