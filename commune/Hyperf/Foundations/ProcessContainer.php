<?php

/**
 * Class ProcessContainer
 * @package Commune\Hyperf\Foundations
 */

namespace Commune\Hyperf\Foundations;


use Commune\Container\ContainerContract;
use Commune\Container\ContainerTrait;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface as Container;
use Psr\Container\ContainerInterface;

/**
 * 通过 hyperf 的容器生成 commune chatbot 的进程级容器.
 * 并执行默认的初始化绑定.
 *
 * Hyperf container adapter for commune chatbot process level container
 */
class ProcessContainer implements ContainerContract
{
    use ContainerTrait;

    const HYPERF_CONTAINER_ID = 'hyperf.container';

    protected $fromHyperf = [
        ClientFactory::class,
        RedisFactory::class,
        ConnectionResolverInterface::class,
    ];

    public function __construct(Container $container, array $bindings = [])
    {
        // 要提前绑定.
        $this->instance(ContainerInterface::class, $container);
        $this->instance(self::HYPERF_CONTAINER_ID, $container);

        // 分享 sharing
        foreach ($bindings as $sharing) {
            $this->instance($sharing, $container->get($sharing));
        }
    }

}