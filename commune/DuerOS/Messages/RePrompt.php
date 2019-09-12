<?php

/**
 * Class Reprompt
 * @package Commune\DuerOS\Messages
 */

namespace Commune\DuerOS\Messages;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\SSML;
use Commune\Chatbot\Blueprint\Message\Tags\NoTranslate;
use Commune\Chatbot\Framework\Messages\Reply;

class RePrompt extends Reply implements NoTranslate
{
    /**
     * @var Message
     */
    protected $rePrompt;

    public function __construct(Message $message)
    {
        $this->rePrompt = $message;
        parent::__construct('' );
    }

    /**
     * @return Message
     */
    public function getRePrompt(): Message
    {
        return $this->rePrompt;
    }

    public function getText(): string
    {
        if ($this->rePrompt instanceof SSML) {
            return $this->rePrompt->getFormatted();
        }
        return $this->rePrompt->getText();
    }

    public function isEmpty(): bool
    {
        return true;
    }
}