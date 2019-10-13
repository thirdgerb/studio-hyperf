<?php

/**
 * Class AnalyseCommandsPipe
 * @package Commune\Studio\SessionPipes
 */

namespace Commune\Studio\SessionPipes;


use Commune\Chatbot\App\Commands\AnalyserPipe;
use Commune\Chatbot\App\Commands\Analysis\ContextRepoCmd;
use Commune\Chatbot\App\Commands\Analysis\RedirectCmd;
use Commune\Chatbot\App\Commands\Analysis\RunningSpyCmd;
use Commune\Chatbot\App\Commands\Analysis\WhereCmd;
use Commune\Chatbot\App\Commands\Analysis\WhoAmICmd;
use Commune\Chatbot\OOHost\Command\HelpCmd;

class AnalyseCommandsPipe extends AnalyserPipe
{
    // 命令的名称.
    protected $commands = [
        HelpCmd::class,
        WhoAmICmd::class,
        WhereCmd::class,
        RedirectCmd::class,
        ContextRepoCmd::class,
        RunningSpyCmd::class,
    ];


}