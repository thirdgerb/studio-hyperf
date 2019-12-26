<?php

/**
 * Class ProcessContainerFactory
 * @package Commune\Hyperf\Foundations
 */

namespace Commune\Hyperf\Foundations;


use Commune\Chatbot\Framework\ChatApp;
use Commune\Hyperf\Foundations\Drivers\StdConsoleLogger;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;

class ChatAppFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ProcessContainerFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function __invoke()
    {
        $botOption = $this->container->get(AppServerOption::class);
        $processContainer = new ProcessContainer($this->container, $botOption->shares);

        $chatbotConfig = $botOption->chatbot->toArray();

        $hyperfStdLogger = $this->container->get(StdoutLoggerInterface::class);
        $consoleLogger = new StdConsoleLogger($hyperfStdLogger);
        $chatApp = new ChatApp($chatbotConfig, $processContainer, $consoleLogger);
        return $chatApp;
    }

}