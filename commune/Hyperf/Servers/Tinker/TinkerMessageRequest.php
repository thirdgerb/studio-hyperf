<?php

/**
 * Class Request
 * @package Commune\Hyperf\Servers\Tinker
 */

namespace Commune\Hyperf\Servers\Tinker;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Conversation\MessageRequestHelper;
use Commune\Support\Uuid\HasIdGenerator;
use Symfony\Component\Console\Style\SymfonyStyle;

class TinkerMessageRequest implements MessageRequest, HasIdGenerator
{
    use MessageRequestHelper;

    /**
     * @var TinkerOption
     */
    protected $option;

    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @var string|Message
     */
    protected $line;


    /*---- cache ----*/

    /**
     * @var ConversationMessage[]
     */
    protected $buffer = [];

    /**
     * Request constructor.
     * @param TinkerOption $option
     * @param SymfonyStyle $output
     * @param Message|string $line
     */
    public function __construct(TinkerOption $option, SymfonyStyle $output, $line)
    {
        $this->option = $option;
        $this->output = $output;
        $this->line = $line;
    }


    /**
     * @return string|Message
     */
    public function getInput()
    {
        return $this->line;
    }

    public function getChatbotUserId(): string
    {
        return $this->option->chatbotUserId;
    }

    public function getPlatformId(): string
    {
        return TinkerChatServer::class;
    }

    protected function makeInputMessage($input): Message
    {
        return new Text($input);
    }


    public function fetchUserId(): string
    {
        return $this->option->userId;
    }

    public function fetchUserName(): string
    {
        return $this->option->userName;
    }

    public function fetchUserData(): array
    {
        return [];
    }

    public function bufferConversationMessage(ConversationMessage $message): void
    {
        $this->buffer[] = $message;
    }

    public function flushChatMessages(): void
    {
        foreach ($this->buffer as $message) {

            $msg = $message->getMessage();
            $text = $msg->getText();

            if ($msg instanceof VerboseMsg) {

                switch($msg->getLevel()) {
                    case VerboseMsg::ERROR :
                        $this->output->error($text);
                        break;
                    case VerboseMsg::WARN :
                        $this->output->warning($text);
                        break;
                    default :
                        $this->output->writeln("<info>$text</info>");
                        break;
                }
            } else {
                $this->output->writeln($msg->getText());
            }
        }

        $this->buffer = [];
    }


}