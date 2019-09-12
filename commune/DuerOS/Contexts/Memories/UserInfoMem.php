<?php

/**
 * Class UserInfo
 * @package Commune\DuerOS\Demo\Memories
 */

namespace Commune\DuerOS\Contexts\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property string $name 用户名字
 */
class UserInfoMem extends MemoryDef
{
    const SCOPE_TYPES = [Scope::USER_ID];
}