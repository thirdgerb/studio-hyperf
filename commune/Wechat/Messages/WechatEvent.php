<?php

/**
 * Class WechatEvent
 * @package Commune\Chatbot\Wechat\Messages
 */

namespace Commune\Wechat\Messages;

use Commune\Chatbot\Framework\Messages\AbsEventMsg;

class WechatEvent extends AbsEventMsg
{
    protected $eventName;

    public function __construct(string $eventName)
    {
        $this->eventName = $eventName;
        parent::__construct();
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

}