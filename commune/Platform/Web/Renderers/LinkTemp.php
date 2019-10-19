<?php


namespace Commune\Platform\Web\Renderers;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\Replies\LinkMsg;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;

class LinkTemp implements ReplyTemplate
{
    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        if ($reply instanceof LinkMsg) {
            $text = $reply->getText();
            $url = $reply->getUrl();
            $text = '<a href="' . $url . '">' . $text . '</a>';
            return [new Text($text)];
        }

        return [ $reply ];
    }

}