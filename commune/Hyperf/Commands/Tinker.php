<?php

/**
 * Class Tinker
 * @package Commune\Hyperf\Commands
 */

namespace Commune\Hyperf\Commands;

use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\Framework\ChatApp;
use Commune\Hyperf\Foundations\Dependencies\StdConsoleLogger;
use Commune\Hyperf\Foundations\ProcessContainer;
use Commune\Hyperf\Servers\Tinker\TinkerChatServer;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Container;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Hyperf\Command\Annotation\Command;

/**
 * 基于symfony command 的chatbot.
 * 通常用于测试.
 *
 * @Command
 */
class Tinker extends SymfonyCommand
{
    const CONFIG_KEY = 'tinker';

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct('commune:tinker');
        $this->setDescription('run chatbot in tinker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // output 组装.
        $output = new SymfonyStyle($input, $output);

        // chatbot server 组装.

        // config
        $config = $this->container->get(ConfigInterface::class);
        $communeConfig = $config->get('commune');
        $tinkerConfig = $communeConfig[self::CONFIG_KEY] ?? [];

        if (empty($tinkerConfig)) {
            $output->error('tinker config not found, should be in config/autoload/commune.php ');
            exit(0);
        }


        // process container
        $workerContainer = new ProcessContainer($this->container);

        // chatbot app
        $chatApp = new ChatApp(
            $tinkerConfig,
            $workerContainer,
            $this->container->get(StdConsoleLogger::class)
        );


        // server
        $server = new TinkerChatServer($chatApp, $output);
        $workerContainer->instance(ChatServer::class, $server);

        $server->run();
    }

}