<?php

/**
 * Class System
 * @package Commune\DuerOS\Contracts\Context
 */

namespace Commune\DuerOS\Contracts\Request;


interface System
{
    public function getUser() : User;

}