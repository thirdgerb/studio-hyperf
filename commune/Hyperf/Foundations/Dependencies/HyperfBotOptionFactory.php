<?php

/**
 * Class HyperfBotOptionFactory
 * @package Commune\Hyperf\Options
 */

namespace Commune\Hyperf\Foundations\Dependencies;


class HyperfBotOptionFactory
{
    protected $option;

    public function set(HyperfBotOption $option)
    {
        $this->option = $option;
    }

    public function __invoke()
    {
        return $this->option;
    }

}