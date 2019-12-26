<?php


namespace Commune\Platform\Web\Servers;


use Swoole\Server;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Commune\Hyperf\Foundations\Requests\SwooleHttpMessageRequest;
use Commune\Platform\Web\Contracts\ResponseRender;
use Commune\Platform\Web\WebComponent;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Commune\Chatbot\OOHost\Dialogue\NeedDialogStatus;

class WebRequest extends SwooleHttpMessageRequest implements NeedDialogStatus
{
    const SCENE_GETTER = 'scene';

    const USER_ID_KEY = 'userId';

    const CONTENT_KEY = 'text';

    const TOKEN_HEADER = 'Authorization';

    /**
     * @var WebComponent
     */
    protected $config;

    /**
     * @var string
     */
    protected $userId;


    /**
     * @var string
     */
    protected $userName = '';

    /**
     * @var array
     */
    protected $userData = [];

    /**
     * @var ResponseRender|null
     */
    protected $apiRender;

    /**
     * @var int
     */
    protected $errCode = 0;

    /**
     * @var string
     */
    protected $errMsg = 'success';

    public function __construct(AppServerOption $botOption, WebComponent $config, Server $server, SwooleRequest $request, SwooleResponse $response)
    {
        $input = $this->parseInput($request);
        $this->config = $config;
        parent::__construct($botOption, $input, $server, $request, $response);
    }


    /**
     * 渲染消息, 但不要输出. 留到 flushResponse 进行真输出.
     * @param ConversationMessage[] $messages
     */
    protected function renderChatMessages(array $messages): void
    {
        $this->getApiRender()->receiveMessages($messages);
    }


    public function getApiRender() : ResponseRender
    {
        return $this->apiRender
            ?? $this->apiRender = $this->conversation->make(ResponseRender::class);
    }


    protected function flushResponse(): void
    {
        $response = $this->getSwooleResponse();
        $response->status(200);
        $response->header('Content-Type', 'application/json');
        $output = $this->getApiRender()->renderOutput();
        $data = [
            'code' => $this->errCode,
            'msg' => $this->errMsg,
            'data' => empty($output) ? new \stdClass() : $output,
        ];
        $response->write(json_encode($data));
    }

    public function sendRejectResponse(): void
    {
        $this->errCode = $this->errCode > 0 ? $this->errCode : 400;
        $this->errMsg = empty($this->errMsg) ? 'invalid message' : $this->errMsg;
        $this->flushResponse();
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
        return $this->validateMethod()
            ?? $this->validateUserId()
            ?? $this->validateLength()
            ?? true;
    }

    protected function validateUserId() : ? bool
    {

        $userId = $this->getSwooleRequest()->cookie[static::USER_ID_KEY] ?? '';
        if (!empty($userId)) {
           $this->userId = $userId;
        } else {
            $this->userId = $this->createUuId();
            $this->getSwooleResponse()->cookie(static::USER_ID_KEY, $this->userId);
        }

        return null;
    }

    protected function validateLength() : ? bool
    {
        $input = $this->getInput();

        if (mb_strlen($input) > $this->config->maxInputLength) {
            $this->errCode = 400;
            $this->errMsg = 'input is too long, must less then '. $this->config->maxInputLength;
            return false;
        }

        return null;
    }

    protected function getMethod() : string
    {
        return $this->getSwooleRequest()->server['request_method'] ?? '';
    }


    protected function validateMethod() : ? bool
    {
        $method = $this->getMethod();
        if ( $method === 'POST') {
            return null;
        }

        $this->errCode = 405;
        $this->errMsg = 'bad method';
        return false;
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
        return $this->userId ?? $this->userId = $this->createUuId();
    }

    public function fetchUserName(): string
    {
        return $this->userName;
    }

    public function fetchUserData(): array
    {
        return $this->userData;
    }


    /**
     * @param string $input
     * @return Message
     */
    protected function makeInputMessage($input): Message
    {
        return new Text($input);
    }

    public function logDialogStatus(Dialog $dialog): void
    {
        $this->getApiRender()->receiveDialog($dialog);
    }

}