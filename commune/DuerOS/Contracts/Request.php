<?php

/**
 * Class MessageRequest
 * @package Commune\DuerOS\Bridges
 */

namespace Commune\DuerOS\Contracts;

/**
 * duerOS request implements
 */
interface Request
{

    public function getVersion() : string;

    public function getSession() : Session;

}