<?php

/**
 * Class DuerOSRequest
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Messages\Events\ConnectionEvt;
use Commune\Chatbot\Framework\Messages\Events\QuitEvt;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Hyperf\Foundations\Requests\AbstractMessageRequest;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Baidu\Duer\Botsdk\Request as DuerRequest;
use Baidu\Duer\Botsdk\Response as DuerResponse;
use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Swoole\Server;

/**
 * @method SwooleRequest getInput()
 */
class DuerOSRequest extends AbstractMessageRequest
{

    /*--------- property ---------*/

    /**
     * @var SwooleResponse
     */
    protected $response;


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
     * @var string
     */
    protected $outSpeech = '';

    /**
     * @var DuerOSCertificate
     */
    protected $certificate;

    /**
     * @var DuerOSNLUParser
     */
    protected $nluParser;

    /**
     * DuerOSRequest constructor.
     * @param HyperfBotOption $option
     * @param Server $server
     * @param SwooleRequest $input
     * @param SwooleResponse $response
     * @param string|null $privateKey
     */
    public function __construct(
        HyperfBotOption $option,
        Server $server,
        SwooleRequest $input,
        SwooleResponse $response,
        string $privateKey
    )
    {
        $this->response = $response;
        $this->response->header('Content-Type', 'application/json;charset=utf-8');
        parent::__construct($option, $input, $input->fd, $server);
        $rawInput = static::fetchRawInputOfRequest($input);
        $this->certificate = new DuerOSCertificate(
            $privateKey,
            $input->server,
            $rawInput
        );

        $this->duerRequest = static::wrapBotRequest($rawInput);
        $this->duerResponse = static::wrapBotResponse($this->duerRequest);
        $this->duerResponse->setShouldEndSession(false);
        $this->nluParser = new DuerOSNLUParser($this->duerRequest);
    }

    /**
     * @return DuerOSCertificate
     */
    public function getCertificate(): DuerOSCertificate
    {
        return $this->certificate;
    }

    public function verify() : bool
    {
        return $this->certificate->verifyRequest();
    }

    public function illegalResponse() :void
    {
        $this->response->end($this->duerResponse->illegalRequest());
    }





    public static function fetchRawInputOfRequest(SwooleRequest $request) : string
    {
        $psr7Request = Psr7Request::loadFromSwooleRequest($request);
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
        if ($message instanceof VerboseMsg) {
            $this->outSpeech .= PHP_EOL . $message->getText();
        }
    }

    protected function flushResponse(): void
    {
        $output =$this->duerResponse->build([
            'outputSpeech' => trim($this->outSpeech)
        ]);

        $this->response->end($output);
    }

    public function getPlatformId(): string
    {
        return DuerOSServer::class;
    }

    public function fetchUserId(): string
    {
        return $this->duerRequest->getUserId() ?? '';
    }

    public function fetchUserName(): string
    {
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
            return new ConnectionEvt();
        }
        if ($this->duerRequest->isSessionEndedRequest()) {
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
                //todo bot-sdk 开发规范不够好.
            ?? $this->sessionId = $this->duerRequest->getSession()->sessionId;
    }


    /**
     * @return DuerResponse
     */
    public function getDuerResponse(): DuerResponse
    {
        return $this->duerResponse;
    }

}