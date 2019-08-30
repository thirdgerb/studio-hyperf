<?php

/**
 * Class User
 * @package Commune\DuerOS\Contracts
 */

namespace Commune\DuerOS\Contracts\Request;


interface User
{
    public function getId() : string;

    public function getAccessToken() : string;
}