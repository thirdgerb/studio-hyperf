<?php

/**
 * Class UserCommands
 * @package Commune\Studio\SessionPipes
 */

namespace Commune\Studio\SessionPipes;

use Commune\Chatbot\App\Components\Predefined\Navigation;
use Commune\Chatbot\OOHost\Command\HelpCmd;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Studio\Commands\WhosYourDaddyCmd;

class UserCommandsPipe extends SessionCommandPipe
{
    // 命令的名称.
    protected $commands = [
        HelpCmd::class,
        WhosYourDaddyCmd::class,
        Navigation\RepeatInt::class,
        Navigation\RestartInt::class,
        Navigation\HomeInt::class,
        Navigation\BackwardInt::class,
        Navigation\QuitInt::class,
    ];

    // 定义一个 command mark
    protected $commandMark = '#';



}