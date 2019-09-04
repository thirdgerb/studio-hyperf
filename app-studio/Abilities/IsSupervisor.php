<?php

/**
 * Class IsSupervisor
 * @package Commune\Studio\Abilities
 */

namespace Commune\Studio\Abilities;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\Blueprint\Conversation\Conversation;

class IsSupervisor implements Supervise
{
    public function isAllowing(Conversation $conversation): bool
    {
        return false;
    }


}