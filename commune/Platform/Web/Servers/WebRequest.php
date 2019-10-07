<?php


namespace Commune\Platform\Web\Servers;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\Speech;
use Commune\Chatbot\Blueprint\Message\Media\ImageMsg;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Tags\Conversational;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Hyperf\Foundations\Requests\SwooleHttpMessageRequest;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Server;

class WebRequest extends SwooleHttpMessageRequest
{
    const SCENE_GETTER = 'scene';

    const USER_ID_KEY = 'userId';

    const CONTENT_KEY = 'text';

    /**
     * @var string
     */
    protected $userId;

    protected $messages = [];

    protected $suggestions = [];

    public function __construct(HyperfBotOption $botOption, Server $server, SwooleRequest $request, SwooleResponse $response)
    {
        $input = $this->parseInput($request);
        parent::__construct($botOption, $input, $server, $request, $response);
    }


    /**
     * 渲染消息, 但不要输出. 留到 flushResponse 进行真输出.
     * @param ConversationMessage[] $messages
     */
    protected function renderChatMessages(array $messages): void
    {

        foreach ($messages as $message) {
            $msg = $message->getMessage();

            if ($msg instanceof Conversational) {
                $this->suggestions = $msg->getSuggestions();
            }

            if ($msg instanceof VerboseMsg) {
                $text = str_replace(
                    "\n",
                    '<br>',
                    htmlentities($msg->getText())
                );
                $this->messages[] = $this->wrapLevel($msg->getLevel(), $text);

            } elseif ($msg instanceof ImageMsg) {
                $this->messages[] = "<img src=\"{$msg->getUrl()}\" />";

            } else {
                $this->messages[] = str_replace(
                    "\n",
                    '<br>',
                    htmlentities($msg->toPrettyJson())
                );
            }
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

    protected function flushResponse(): void
    {
        $response = $this->getSwooleResponse();
        $response->status(200);
        $response->header('Content-Type', 'application/json');
        $result = [
            'says' => $this->messages
        ];

        $replies = [];
        if (!empty($this->suggestions)) {
            foreach ($this->suggestions as $index => $suggestion) {
                if (is_numeric($index)) {
                    $replies[] = ['question' => $suggestion, 'answer' => $suggestion];
                } else {
                    $replies[] = ['question'=> $index, 'answer' => $suggestion];
                }
            }
        }
        $result['reply'] = $replies;
        $response->write(json_encode($result));
    }

    public function sendRejectResponse(): void
    {
        $response = $this->getSwooleResponse();
        $response->status(400);
        $response->write('invalid request');
    }

    public function parseInput(SwooleRequest $request) : string
    {
        $content = $request->rawContent();
        if (empty($content)) {
            return '';
        }

        $json = json_decode($content, true);
        return is_array($json) && isset($json['text'])
            ? (string) $json['text']
            : '';
    }


    protected function doValidate(): bool
    {
        $request = $this->getSwooleRequest();
        // 没有入参
        return strlen($this->input)
            // 非 post 方法
            && (
                ($request->server['request_method'] ?? '' ) === 'POST'
            );
    }

    public function getScene(): ? string
    {
        $scene = $this->getSwooleRequest()->get[static::SCENE_GETTER] ?? null;
        return $scene;
    }

    public function getPlatformId(): string
    {
        return WebServer::class;
    }

    public function fetchUserId(): string
    {
        if (isset($this->userId)) {
            return $this->userId;
        }

        $userId = $this->getSwooleRequest()->cookie[static::USER_ID_KEY] ?? '';
        if (!empty($userId)) {
            return $this->userId = $userId;
        }

        $this->userId = $this->createUuId();
        $this->getSwooleResponse()->cookie(static::USER_ID_KEY, $this->userId);
        return $this->userId;
    }

    public function fetchUserName(): string
    {
        return '';
    }

    public function fetchUserData(): array
    {
        return [];
    }


    /**
     * @param string $input
     * @return Message
     */
    protected function makeInputMessage($input): Message
    {
        return new Text($input);
    }


}