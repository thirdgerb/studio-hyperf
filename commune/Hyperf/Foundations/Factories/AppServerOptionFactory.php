<?php

namespace Commune\Hyperf\Foundations\Factories;

use Commune\Hyperf\Foundations\Options\AppServerOption;

class AppServerOptionFactory
{
    protected $option;

    public function set(AppServerOption $option)
    {
        $this->option = $option;
    }

    public function __invoke()
    {
        return $this->option;
    }

}