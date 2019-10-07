<?php


namespace Commune\Platform\Wechat\Messages;


interface WechatMedia extends WechatMessage
{

    public function getMediaId() : string;

}