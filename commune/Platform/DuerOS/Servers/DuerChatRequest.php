<?php

/**
 * Class DuerChatRequest
 * @package Commune\Platform\DuerOS\Servers
 */

namespace Commune\Platform\DuerOS\Servers;


use Commune\Chatbot\App\Messages\Media\Audio;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Replies\SSML;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\App\Messages\Events\QuitEvt;
use Commune\Chatbot\App\Messages\Events\StartEvt;
use Commune\Platform\DuerOS\Constants\EndSession;
use Commune\Platform\DuerOS\DuerOSComponent;
use Commune\Platform\DuerOS\Events\DialogComplete;
use Commune\Platform\DuerOS\Messages\AbsCard;
use Commune\Platform\DuerOS\Messages\AbsDirective;
use Commune\Platform\DuerOS\Messages\RePrompt;
use Commune\Platform\DuerOS\Mod\DirectivePlaceHolder;
use Commune\Platform\DuerOS\Templates\AbstractTemp;
use Commune\Hyperf\Foundations\Options\AppServerOption;
use Commune\Hyperf\Foundations\Requests\SwooleHttpMessageRequest;
use Commune\Hyperf\Support\HttpBabel;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Baidu\Duer\Botsdk\Request as DuerRequest;
use Baidu\Duer\Botsdk\Response as DuerResponse;
use Swoole\Server;

/**
 * @method SwooleRequest getInput()
 */
class DuerChatRequest extends SwooleHttpMessageRequest
{

    /*--------- property ---------*/

    /**
     * @var DuerOSComponent
     */
    protected $duerOSOption;

    /**
     * @var DuerRequest
     */
    protected $duerRequest;

    /**
     * @var DuerResponse
     */
    protected $duerResponse;

    /*--------- cached ---------*/

    /**
     * @var string|null;
     */
    protected $sessionId;

    /**
     * @var NLU
     */
    protected $nlu;

    /**
     * @var string
     */
    protected $traceId;


    /**
     * @var DuerOSCertificate
     */
    protected $certificate;

    /**
     * @var DuerOSNLUParser
     */
    protected $nluParser;

    /**
     * @var bool
     */
    protected $valid;

    /*--------- output --------*/

    /**
     * 永远是 ssml
     * @var string
     */
    public $outSpeech = '';

    /**
     * @var array
     */
    public $directives = [];

    /**
     * @var array
     */
    public $cards = [];

    /**
     * @var string
     */
    public $rePrompt;

    /**
     * DuerChatRequest constructor.
     * @param AppServerOption $option
     * @param DuerOSComponent $duerOSOption
     * @param DuerChatServer $server
     * @param SwooleRequest $input
     * @param SwooleResponse $response
     * @param string $rawInput
     * @param string $privateKeyContent
     */
    public function __construct(
        AppServerOption $option,
        DuerOSComponent $duerOSOption,
        Server $server,
        LoggerInterface $logger,
        SwooleRequest $request,
        SwooleResponse $response,
        string $rawInput,
        string $privateKeyContent
    )
    {
        $this->request = $request;
        $this->response = $response;
        $this->duerOSOption = $duerOSOption;
        $this->response->header('Content-Type', 'application/json;charset=utf-8');

        parent::__construct(
            $option,
            $rawInput,
            $server,
            $request,
            $response
        );

        // 校验环节
        $symfonyRequest = HttpBabel::requestFromSwooleToSymfony($request);
        $this->certificate = new DuerOSCertificate(
            $logger,
            $privateKeyContent,
            $symfonyRequest->server->all(),
            $rawInput
        );

        $this->duerRequest = static::wrapBotRequest($rawInput);
        $this->duerResponse = static::wrapBotResponse($this->duerRequest);


        $this->duerResponsePolicy();
        $this->nluParser = new DuerOSNLUParser($this->duerRequest, $this->duerOSOption);

        // 默认回复
        $this->rePrompt = $this->duerOSOption->rePrompt;
    }

    /**
     * 默认绑定.
     */
    protected function onBindConversation() : void
    {
        parent::onBindConversation();

        $this->conversation->share(DuerRequest::class, $this->duerRequest);
        $this->conversation->share(DuerResponse::class, $this->duerResponse);
    }

    /**
     * 校验请求是否正确.
     * @return bool
     */
    protected function doValidate(): bool
    {
        // 记录请求日志
        $this->logger->info(
            'DuerChatRequest getRequest',
            [
                'logId' => $this->duerRequest->getLogId(),
                'duerRequest' => $this->duerRequest->getData()['request'] ?? [],
                'duerUserId' => $this->duerRequest->getUserId(),
                'duerSession' => $this->duerRequest->getData()['session'] ?? [],
                'sig' => $this->certificate->getRequestSig(),
                'cert' => $this->certificate->getSignatureCertUrl(),
            ]
        );

        if (!$this->botOption->debug) {
            $this->getCertificate()->enableVerifyRequestSign();
        }

        $verified = $this->verify();

        if (!$verified) {
            $this->logger->warning(
                static::class . ' request failed certificate',
                [
                    'debug' => $this->botOption->debug,
                    'servers' => $this->getSwooleRequest()->server,
                ]
            );
        }

        return $verified;
    }


    /**
     * duer os 响应的默认策略.
     * @see AbstractTemp   看看基础策略怎么实现的.
     */
    protected function duerResponsePolicy()
    {
        // 默认多轮对话不结束, 除非主动返回 quit
        $this->duerResponse->setShouldEndSession(false);

        // 默认关闭dueros 的聆听.
        // 除非主动用 question 的方式来进行对话, 在模板中响应.
        $this->duerResponse->setExpectSpeech(false);
    }

    /**
     * @return DuerOSCertificate
     */
    public function getCertificate(): DuerOSCertificate
    {
        return $this->certificate;
    }

    /**
     * 执行 duerOS 的校验.
     * @return bool
     */
    public function verify() : bool
    {
        $userId = $this->duerRequest->getUserId();
        if (empty($userId)) {
            return false;
        }

        // todo 埋点做记录
        return $this->certificate->verifyRequest();
    }

    /**
     * 请求不正确, 返回失败信息.
     */
    public function sendRejectResponse() :void
    {
        $this->response->write($this->duerResponse->illegalRequest());
    }

    public static function fetchRawInputOfRequest(SwooleRequest $request) : string
    {
        $psr7Request = HttpBabel::requestFromSwooleToPSR($request);
        // prepare duer os bot request
        $rawInput = $psr7Request->getBody()->getContents();
        return $rawInput;
    }

    public static function wrapBotRequest(string $rawInput) : DuerRequest
    {
        $rawInput = str_replace("", "", $rawInput);
        $postData = json_decode($rawInput, true);
        return new DuerRequest($postData);
    }

    public static function wrapBotResponse(DuerRequest $request) : DuerResponse
    {
        return new DuerResponse(
            $request,
            $request->getSession(),
            $request->getNlu()
        );
    }

    /**
     * @return DuerRequest
     */
    public function getDuerRequest(): DuerRequest
    {
        return $this->duerRequest;
    }


    /**
     * @param ConversationMessage[] $messages
     */
    protected function renderChatMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->renderMessage($message);
        }
    }

    protected function renderMessage(ConversationMessage $reply) : void
    {
        $message = $reply->getMessage();

        // audio
        if ($message instanceof Audio) {
            $url = $message->getUrl();
            $this->outSpeech .= '<audio src ="'.$url.'"></audio>';

        // ssml
        } elseif ($message instanceof SSML) {
            $this->outSpeech .= $message->getFormatted();

        // 有特殊的 Reprompt
        } elseif ($message instanceof RePrompt) {
            $this->rePrompt = $message->getText();

        // 命令
        } elseif ($message instanceof AbsDirective) {
            $this->directives[] = new DirectivePlaceHolder($message);

        // 卡片
        } elseif ($message instanceof AbsCard) {
            $this->cards[] = $message->toCardArray();

        // verbose
        } elseif ($message instanceof VerbalMsg) {
            $this->outSpeech .= $message->getText();
        }


    // todo 还有模板. body template

        // 其它情况暂不处理.
    }

    protected function flushResponse(): void
    {
        $data = [];
        // todo 目前没有别的办法处理了. 除非duerOS 修改有问题的api
        $data['reprompt'] = $this->rePrompt;

        if (!empty($this->outSpeech)) {
            $data['outputSpeech'] = '<speak>'.trim($this->outSpeech) .'</speak>';
        }

        if (!empty($this->directives)) {
            $data['directives'] = $this->directives;
        }

        if (!empty($this->cards)) {
            $data['card'] = $this->cards;
        }

        $output =$this->duerResponse->build($data);

        // 记录有效request的日志
        $logger = $this->conversation->getLogger();
        $logger->info('DuerChatRequest queryAndReply', [
            'logId' => $this->duerRequest->getLogId(),
            'query' => $this->duerRequest->getQuery(),
            'outSpeech' => $data['outputSpeech'] ?? '',
        ]);
        $logger->info(
            'DuerChatRequest finishResponse',
            [
                'logId' => $this->duerRequest->getLogId(),
                'output' => $output,
            ]
        );

        // 触发事件, 可用于记录来回消息.
        $event = new DialogComplete(
            $this->conversation->getTraceId(),
            $this->input,
            $output
        );
        $this->conversation->fire($event);

        // 完成渲染并退出.
        $this->response->end($output);
    }

    public function getPlatformId(): string
    {
        return DuerChatServer::class;
    }

    public function fetchUserId(): string
    {
        return $this->duerRequest->getUserId() ?? '';
    }

    public function fetchUserName(): string
    {
        //todo 未来实现api
        return '';
    }

    /**
     * todo 需要设计api调用.
     * @return array
     */
    public function fetchUserData(): array
    {
        return $this->duerRequest->getUserInfo() ?? [];
    }

    /**
     * @param SwooleRequest $input   ignore
     * @return Message
     */
    protected function makeInputMessage($input): Message
    {
        if ($this->duerRequest->isLaunchRequest()) {
            return new StartEvt();
        }

        if ($this->duerRequest->isSessionEndedRequest()) {
            $this->handleEndSession();
            return new QuitEvt();
        }

        return new Text($this->duerRequest->getQuery());
    }

    public function fetchNLU(): ? NLU
    {
        return $this->nlu ?? $this->nlu = $this->nluParser->parseNLU();

    }

    public function fetchMessageId(): string
    {
        return $this->messageId = $this->duerRequest->getLogId() ?? $this->generateMessageId();
    }

    public function fetchSessionId(): ? string
    {
        return $this->sessionId
            //todo bot-sdk 开发规范不够好. 可能出现类型问题.
            ?? $this->sessionId = $this->duerRequest->getSession()->sessionId;
    }


    /**
     * @return DuerResponse
     */
    public function getDuerResponse(): DuerResponse
    {
        return $this->duerResponse;
    }


    protected function handleEndSession() : void
    {
        $data = $this->duerRequest->getData();
        $reason = $data['request']['reason'] ?? '';

        switch ($reason) {
            case EndSession::EXCEEDED_MAX_REPROMPTS:
                $this->warn("end session because of $reason");

                break;
            case EndSession::ERROR :
                $error = $data['request']['error']['type'] ?? '';
                $message = $data['request']['error']['message'] ?? '';
                $this->handleErrorEndSession($error, $message);
                break;
            case EndSession::USER_INITIATED :
            default:
                return;
        }

    }

    protected function handleErrorEndSession(string $error, string $message) : void
    {
        switch ($error) {
            case EndSession::ERROR_INVALID_RESPONSE:
                $this->error("end session because of $error, $message");
                break;

            case EndSession::ERROR_DEVICE_COMMUNICATION_ERROR:
            case EndSession::ERROR_INTERNAL_ERROR:
            default:
                $this->warn("end session because of $error, $message");
        }

    }

    protected function warn(string $message, array $context = []) : void
    {
        $this->conversation
            ->getLogger()
            ->warning(
                "DuerChatRequest warning, $message",
                $this->wrapContext($context)
            );
    }

    protected function error(string $message, array $context = []) : void
    {
        $this->conversation
            ->getLogger()
            ->error(
                "DuerChatRequest error, $message",
                $this->wrapContext($context)
            );

    }

    protected function wrapContext(array $context) : array
    {
        return [
            'name' => $this->duerOSOption->name,
            'sessionId' => $this->getDuerRequest()->getSession()->sessionId,
            'requestId' => $this->getDuerRequest()->getLogId(),
            'userId' => $this->getDuerRequest()->getUserId(),
        ] + $context;
    }


    public static function getMockingQuery(SwooleRequest $request) : string
    {
        return $request->get['mocking'] ?? '';
    }


    public function getScene(): ? string
    {
        return $this->getSwooleRequest()->get['scene'] ?? null;
    }

}