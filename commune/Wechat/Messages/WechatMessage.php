<?php


namespace Commune\Wechat\Messages;


use EasyWeChat\Kernel\Contracts\MessageInterface;

interface WechatMessage
{

    public function toEasyWechatMessage() : MessageInterface;

}