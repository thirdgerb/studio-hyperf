<?php

/**
 * Class DemoComponent
 * @package Commune\Hyperf\Demo
 */

namespace Commune\Hyperf\Demo;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\Demo\DemoComponent;

class HyperfDemoComponent extends ComponentOption
{
    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\Hyperf\\Demo\\",
            __DIR__
        );

        $this->dependComponent(DemoComponent::class);
    }

    public static function stub(): array
    {
        return [];
    }


}