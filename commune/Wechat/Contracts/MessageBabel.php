<?php


namespace Commune\Wechat\Contracts;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Wechat\Servers\OfficialAccountRequest;
use EasyWeChat\Kernel\Contracts\MessageInterface;

/**
 * 默认将 commune chatbot 的 message 转义为 wechat official account 的 message
 *
 * 如果这个接口作为一个服务注册到了 chatApp, 则会自动进行转义.
 *
 * @see OfficialAccountRequest
 */
interface MessageBabel
{
    public function transform(Message $message) : MessageInterface;
}