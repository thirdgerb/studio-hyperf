<?php

/**
 * Class ProcessContainer
 * @package Commune\Hyperf\Foundations
 */

namespace Commune\Hyperf\Foundations;


use Commune\Container\ContainerContract;
use Commune\Container\ContainerTrait;
use Commune\Hyperf\Foundations\Dependencies\HyperfBotOption;
use Psr\Container\ContainerInterface as Container;

/**
 * Hyperf container adapter for commune chatbot process level container
 */
class ProcessContainer implements ContainerContract
{
    use ContainerTrait;

    public function __construct(Container $container)
    {
        $this->instance(Container::class, $container);
        // 要提前绑定.
        $this->instance(HyperfBotOption::class, $container->get(HyperfBotOption::class));
    }

}