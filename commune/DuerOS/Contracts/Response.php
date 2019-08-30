<?php

/**
 * Class Response
 * @package Commune\DuerOS\Contracts
 */

namespace Commune\DuerOS\Contracts;


interface Response
{
    public function getVersion() : string;

    public function getRequest() : Request;

    public function getSession() : Session;


}