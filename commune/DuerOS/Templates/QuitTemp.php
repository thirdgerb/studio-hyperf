<?php

/**
 * Class QuitTemp
 * @package Commune\DuerOS\Templates
 */

namespace Commune\DuerOS\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\DuerOS\Servers\DuerOSRequest;

class QuitTemp implements ReplyTemplate
{
    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * QuitTemp constructor.
     * @param ChatbotConfig $chatbotConfig
     */
    public function __construct(ChatbotConfig $chatbotConfig)
    {
        $this->chatbotConfig = $chatbotConfig;
    }


    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        $msgId = $this->chatbotConfig->defaultMessages->farewell;
        $text = $conversation->getSpeech()->trans(strval($msgId));

        $request = $conversation->getRequest();
        if ($request instanceof DuerOSRequest) {
            $request->getDuerResponse()->setShouldEndSession(true);
        }

        return [new Text($text)];
    }


}