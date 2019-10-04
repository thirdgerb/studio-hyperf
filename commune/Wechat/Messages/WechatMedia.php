<?php


namespace Commune\Wechat\Messages;


interface WechatMedia extends WechatMessage
{

    public function getMediaId() : string;

}