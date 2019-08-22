<?php

/**
 * Class ProcessContainerFactory
 * @package Commune\Hyperf\Foundations
 */

namespace Commune\Hyperf\Foundations;


use Commune\Chatbot\Framework\ChatApp;
use Commune\Hyperf\Foundations\Dependencies\HyperfBotOption;
use Commune\Hyperf\Foundations\Dependencies\StdConsoleLogger;
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
        $processContainer = $this->container->get(ProcessContainer::class);
        $botOption = $this->container->get(HyperfBotOption::class);

        $chatbotConfig = $botOption->chatbot->toArray();
        $logger = $this->container->get(StdConsoleLogger::class);
        $chatApp = new ChatApp($chatbotConfig, $processContainer, $logger);
        return $chatApp;
    }

}