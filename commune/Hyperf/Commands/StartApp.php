<?php

/**
 * Class StartApp
 * @package Commune\Hyperf\Servers
 */

namespace Commune\Hyperf\Commands;

use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Hyperf\Foundations\Factories\HyperfBotOptionFactory;
use Commune\Hyperf\Foundations\HyperfChatServer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Command\Annotation\Command;


/**
 * @Command
 */
class StartApp extends SymfonyCommand
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * StartApp constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct('commune:start');
        $this->setDescription('start chatbot application');
        $this->addArgument('chatbot_name', null, 'app name defined at config/autoload/commune');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Swoole\Runtime::enableCoroutine(true);

        $this->checkEnvironment($output);

        // 名称检查
        $name = $input->getArgument('chatbot_name');
        if (empty($name)) {
            $output->writeln('<error>Error</error> chatbot name is required');
            exit(0);
        }

        // 获取配置
        $communeConfig = $this->container
            ->get(ConfigInterface::class)
            ->get('commune');

        $config = $communeConfig['apps'][$name] ?? [];
        if (empty($config)) {
            $output->writeln('<error>Error</error> chatbot '.$name.' not exists');
            exit(0);
        }
        $config['name'] = $name;

        // 绑定option
        $hyperfChatbotOption = new HyperfBotOption($config);
        /**
         * @var HyperfBotOptionFactory $factory
         */
        $factory = $this->container->get(HyperfBotOptionFactory::class);
        $factory->set($hyperfChatbotOption);

        $this->container
            ->get(HyperfChatServer::class)
            ->run();
    }





    protected function checkEnvironment(OutputInterface $output)
    {
        if (ini_get_all('swoole')['swoole.use_shortname']['local_value'] !== 'Off') {
            $output->writeln('<error>ERROR</error> Swoole short name have to disable before start server, please set swoole.use_shortname = \'Off\' into your php.ini.');
            exit(0);
        }
    }

}