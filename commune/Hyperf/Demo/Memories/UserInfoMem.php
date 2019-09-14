<?php

/**
 * Class UserInfo
 * @package Commune\DuerOS\Demo\Memories
 */

namespace Commune\Hyperf\Demo\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property string $name ask.name
 */
class UserInfoMem extends MemoryDef
{
    const DESCRIPTION = '用户基本信息';
    const SCOPE_TYPES = [Scope::USER_ID];
}