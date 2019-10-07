<?php


namespace Commune\Platform\Wechat\Messages;


use EasyWeChat\Kernel\Contracts\MessageInterface;

interface WechatMessage
{

    public function toEasyWechatMessage() : MessageInterface;

}