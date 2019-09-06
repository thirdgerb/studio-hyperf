<?php

/**
 * Class DuerOSDemo
 * @package Commune\DuerOS\Demo
 */

namespace Commune\DuerOS\Demo;


use Commune\Chatbot\Framework\Component\ComponentOption;

class DuerOSDemo extends ComponentOption
{
    protected function doBootstrap(): void
    {
        $this->loadSelfRegisterByPsr4(
            "Commune\\DuerOS\\Demo",
            __DIR__
        );
    }

    public static function stub(): array
    {
        return [];
    }


}