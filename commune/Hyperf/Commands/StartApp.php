<?php

namespace Commune\Hyperf\Commands;

use Commune\Chatbot\Blueprint\Application;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Commune\Hyperf\Foundations\Factories\AppServerOptionFactory;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Command\Annotation\Command;


/**
 * @Command
 * 在hyperf中启动 commune chatbot app 的命令.
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

        // 获取配置
        $communeConfig = $this->container
            ->get(ConfigInterface::class)
            ->get('commune');

        if (empty($name)) {
            $apps = array_keys($communeConfig['apps']);
            $output->writeln('<error>Error</error> chatbot name is required');
            $output->writeln('you may want to start : ');
            foreach ($apps as $app) {
                $output->writeln("- $app");
            }
            exit(0);
        }

        $config = $communeConfig['apps'][$name] ?? [];

        if (empty($config)) {
            $output->writeln('<error>Error</error> chatbot '.$name.' not exists');
            exit(0);
        }

        $config['name'] = $name;
        // 使用配置数组, 绑定hyperf option
        $hyperfChatbotOption = new AppServerOption($config);
        /**
         * @var AppServerOptionFactory $factory
         */
        $factory = $this->container->get(AppServerOptionFactory::class);
        $factory->set($hyperfChatbotOption);

        /**
         * @var Application $app
         */
        $app = $this->container->get(Application::class);

        // 启动.
        $app->getServer()->run();

    }

    protected function checkEnvironment(OutputInterface $output)
    {
        if (ini_get_all('swoole')['swoole.use_shortname']['local_value'] !== 'Off') {
            $output->writeln('<error>ERROR</error> Swoole short name have to disable before start server, please set swoole.use_shortname = \'Off\' into your php.ini.');
            exit(0);
        }
    }

}