<?php

/**
 * Class TestCase
 * @package Commune\DuerOS\Demo
 */

namespace Commune\DuerOS\Demo;


use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class TestCase extends OOContext
{

    const DESCRIPTION = '测试demo';

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('你好啊!')
            ->hearing()
            ->is('你好', function(Dialog $dialog){
                $dialog->say()->info('你也好啊');
                return $dialog->wait();
            })
            ->is('退出', function(Dialog $dialog){
                return $dialog->quit();
            })
            ->end();
    }

    public function __exiting(Exiting $listener): void
    {
    }


}