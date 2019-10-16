<?php


namespace Commune\Platform\Web\Libraries;


use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\Media\ImageMsg;
use Commune\Chatbot\Blueprint\Message\Replies\LinkMsg;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Platform\Web\Contracts\ResponseRender;

class DemoResponseRender implements ResponseRender
{

    protected $messages = [];

    protected $suggestions = [];

    /**
     * @var array
     */
    protected $dialogState = [];

    /**
     * @var string
     */
    protected $contextName = '';

    /**
     * @param ConversationMessage[] $messages
     */
    public function receiveMessages(array $messages): void
    {

        foreach ($messages as $message) {
            $msg = $message->getMessage();

            // 文本渲染
            if ($msg instanceof LinkMsg) {
                $text = $msg->getText();
                $url = $msg->getUrl();
                $text = '<a href="'.$url.'">'.$text. '</a>';

            } elseif ($msg instanceof ImageMsg) {
                $text = "<img src=\"{$msg->getUrl()}\" />";

            } else {
                $text = str_replace(
                    "\n",
                    '<br>',
                    htmlentities($msg->getText())
                );
            }

            // 级别渲染.
            $this->messages[] = $msg instanceof VerboseMsg ? $this->wrapLevel($msg->getLevel(), $text) : $text;
        }
    }


    protected function wrapLevel(string $level, string $text) : string
    {
        switch ($level) {
            case Speech::WARNING :
                return "<color style='color: yellow'>$text</color>";
            case Speech::ERROR :
                return "<color style='color: red'>$text</color>";
            default:
                return $text;
        }
    }

    public function receiveDialog(Dialog $dialog): void
    {
        $question = $dialog->currentQuestion();
        if (isset($question)) {
            $this->suggestions = $question->getSuggestions();
        }

        $this->contextName = $dialog->currentContext()->getName();
    }

    public function renderOutput(): array
    {
        $replies = [];
        if (!empty($this->suggestions)) {
            foreach ($this->suggestions as $index => $suggestion) {
                if (is_int($index)) {
                        $replies[] = ['question' => $suggestion, 'answer' => $suggestion];
                } else {
                    $replies[] = ['question'=> $index, 'answer' => $suggestion];
                }
            }
        }

        return [
            'replies' => [
                'says' => $this->messages,
                'reply' => $replies,
            ],
            'dialog' => [
                'contextName' => $this->contextName
            ],
        ];
    }


}