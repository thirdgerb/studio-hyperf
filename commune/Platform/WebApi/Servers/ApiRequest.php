<?php


namespace Commune\Platform\WebApi\Servers;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\MessageRequest;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Conversation\MessageRequestHelper;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Platform\WebApi\Libraries\ApiResult;
use Commune\Platform\WebApi\WebApiComponent;
use Commune\Support\Uuid\HasIdGenerator;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

class ApiRequest implements MessageRequest, HasIdGenerator, RunningSpy
{
    use MessageRequestHelper, RunningSpyTrait;

    const ACTION_FIELD = 'action';

    /**
     * @var SwooleRequest
     */
    protected $swooleRequest;

    /**
     * @var SwooleResponse
     */
    protected $swooleResponse;


    /**
     * @var WebApiComponent
     */
    protected $config;

    /*----- response -----*/

    protected $errCode = 0;

    protected $errMsg = '';

    protected $resData = [];


    /*----- cached -----*/

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string action class
     */
    protected $action;

    /**
     * @var array
     */
    protected $input = [];

    /**
     * ApiRequest constructor.
     * @param SwooleRequest $swooleRequest
     * @param SwooleResponse $swooleResponse
     * @param WebApiComponent $config
     */
    public function __construct(SwooleRequest $swooleRequest, SwooleResponse $swooleResponse, WebApiComponent $config)
    {
        $this->swooleRequest = $swooleRequest;
        $this->swooleResponse = $swooleResponse;
        $this->config = $config;
        static::addRunningTrace($id = $this->fetchMessageId(), $id);
    }


    protected function onBindConversation() : void
    {
        $this->conversation->share(SwooleRequest::class, $this->swooleRequest);
        $this->conversation->share(SwooleResponse::class, $this->swooleResponse);
    }

    public function getSwooleRequest(): SwooleRequest
    {
        return $this->swooleRequest;
    }

    public function getSwooleResponse(): SwooleResponse
    {
        return $this->swooleResponse;
    }


    public function validate(): bool
    {
        $method = $this->getRequestMethod();
        $scene = $this->getScene();

        // 检查 gets 里是否有 action 参数.
        if (empty($scene)) {
            return false;
        }

        if ($method === 'GET') {
            $getActions = $this->config->getActions;

            if (isset($getActions[$scene])) {
                $this->action = $getActions[$scene];
                $this->input = $this->getSwooleRequest()->get;
                unset($this->input[self::ACTION_FIELD]);
                return true;
            }

        }

        if ($method !== 'POST') {
            $this->errCode = WebApiComponent::CODE_BAD_METHOD;
            $this->errMsg = 'bad method';
            return false;
        }

        $postActions = $this->config->postActions;
        if (!isset($postActions[$scene])) {
            $this->errCode = WebApiComponent::CODE_NOT_FOUND;
            $this->errMsg = 'not found';
            return false;
        }

        $this->action = $postActions[$scene];
        $this->input = $this->getSwooleRequest()->post;
        return true;

    }


    protected function getRequestMethod() : string
    {
        return $this->method
            ?? $this->method = $this->getSwooleRequest()->server['request_method'] ?? '';
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    public function getScene(): ? string
    {
        return $this->action
            ?? $this->action = $this->getSwooleRequest()->get[self::ACTION_FIELD] ?? '';
    }

    public function getPlatformId(): string
    {
        return ApiServer::class;
    }

    public function fetchSessionId(): ? string
    {
        return $this->fetchMessageId();
    }

    public function fetchChatId(): ? string
    {
        return $this->fetchMessageId();
    }

    public function fetchUserId(): string
    {
        return $this->fetchMessageId();
    }

    public function fetchUserName(): string
    {
        return $this->fetchMessageId();
    }

    public function fetchUserData(): array
    {
        return [];
    }

    public function bufferMessage(ConversationMessage $message): void
    {
        return;
    }


    public function sendResponse(): void
    {
        $response = $this->swooleResponse;
        $response->status(200);
        $response->header('Content-Type', 'application/json');

        $res = [
            'code' => $this->errCode,
            'msg' => $this->errMsg,
            'data' => empty($this->resData) ? new \stdClass() : $this->resData,
        ];

        $response->write(json_encode($res));
    }

    public function sendRejectResponse(): void
    {
        $this->errCode = WebApiComponent::CODE_BAD_REQUEST;
        $this->errMsg = 'invalid request';
        $this->resData = [];
        $this->sendResponse();
    }

    public function sendFailureResponse(): void
    {
        $this->errCode = WebApiComponent::CODE_FAILURE;
        $this->errMsg = 'system error';
        $this->resData = [];
        $this->sendResponse();
    }


    protected function makeInputMessage($input): Message
    {
        return new Text($this->getScene());
    }

    public function withResponse(ApiResult $apiResult)  : void
    {
        $this->errCode = $apiResult->code;
        $this->errMsg = $apiResult->msg;
        $this->resData = $apiResult->data;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->fetchMessageId());
    }
}