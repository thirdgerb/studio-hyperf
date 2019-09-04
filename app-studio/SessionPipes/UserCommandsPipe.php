<?php

/**
 * Class UserCommands
 * @package Commune\Studio\SessionPipes
 */

namespace Commune\Studio\SessionPipes;

use Commune\Chatbot\App\Commands\Analysis\WhereCmd;
use Commune\Chatbot\App\Commands\Analysis\WhoAmICmd;
use Commune\Chatbot\App\Components\Predefined\Navigation;
use Commune\Chatbot\OOHost\Command\HelpCmd;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;

class UserCommandsPipe extends SessionCommandPipe
{
    // 命令的名称.
    protected $commands = [
        HelpCmd::class,
        WhereCmd::class,
        WhoAmICmd::class,
        Navigation\RepeatInt::class,
        Navigation\RestartInt::class,
    ];

    // 定义一个 command mark
    protected $commandMark = '#';



}