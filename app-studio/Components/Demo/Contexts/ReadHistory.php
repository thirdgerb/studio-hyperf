<?php


namespace Commune\Studio\Components\Demo\Contexts;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property bool $isStarted
 */
class ReadHistory extends MemoryDef
{
    const SCOPE_TYPES = [Scope::USER_ID];

    protected function init(): array
    {
        return [
            'isStarted' => false
        ];
    }

}