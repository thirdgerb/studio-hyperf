<?php

/**
 * Class AbsDirective
 * @package Commune\DuerOS\Messages
 */

namespace Commune\DuerOS\Messages;


use Commune\Chatbot\Framework\Messages\AbsMessage;

abstract class AbsDirective extends AbsMessage
{
    abstract public function getType() : string;

    abstract public function toDirectiveArray() : array;

    public function getMessageType(): string
    {
        return self::class;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getText(): string
    {
        return '';
    }

    public function toMessageData(): array
    {
        return $this->toDirectiveArray();
    }

    public function namesAsDependency(): array
    {
        $names = parent::namesAsDependency();
        $names[] = self::class;
        return $names;
    }
}