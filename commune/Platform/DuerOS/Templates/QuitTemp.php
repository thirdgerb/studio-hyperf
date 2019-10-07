<?php

/**
 * Class QuitTemp
 * @package Commune\Platform\DuerOS\Templates
 */

namespace Commune\Platform\DuerOS\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Config\ChatbotConfig;

class QuitTemp extends AbstractTemp
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


    public function doRender(ReplyMsg $reply, Conversation $conversation): array
    {
        $msgId = $this->chatbotConfig->defaultMessages->farewell;
        $text = $conversation->getSpeech()->trans(strval($msgId));

        $request = $this->getDuerRequest($conversation);
        if (isset($request)) {
            $request->getDuerResponse()->setShouldEndSession(true);
        }

        return [new Text($text)];
    }


}