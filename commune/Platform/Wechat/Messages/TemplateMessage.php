<?php


namespace Commune\Platform\Wechat\Messages;


use Commune\Chatbot\Framework\Messages\AbsMessage;

abstract class TemplateMessage extends AbsMessage
{
    public function isEmpty(): bool
    {
        return false;
    }

    abstract public function getTemplateData() : array;

    public function getText(): string
    {
        return $this->toJson();
    }

    public function toMessageData(): array
    {
        return $this->getTemplateData();
    }


}