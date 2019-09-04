<?php

/**
 * Class DuerOSRequest
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;


use Commune\Chatbot\App\Components\Predefined\Navigation\HomeInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\QuitInt;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Conversation\NatureLanguageUnit;
use Commune\Chatbot\Framework\Messages\Events\ConnectionEvt;
use Commune\Chatbot\Framework\Messages\Events\QuitEvt;
use Commune\DuerOS\Constants\DuerOSCommonIntents;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Hyperf\Foundations\Requests\AbstractMessageRequest;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Baidu\Duer\Botsdk\Request as BotRequest;
use Baidu\Duer\Botsdk\Response as BotResponse;
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
     * @var BotRequest
     */
    protected $botRequest;

    /**
     * @var BotResponse
     */
    protected $botResponse;

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
     * DuerOSRequest constructor.
     * @param HyperfBotOption $option
     * @param SwooleRequest $input
     * @param SwooleResponse $response
     */
    public function __construct(
        HyperfBotOption $option,
        Server $server,
        SwooleRequest $input,
        SwooleResponse $response
    )
    {
        $this->response = $response;
        $this->response->header('Context-Type', 'application/json;charset=utf-8');
        parent::__construct($option, $input, $input->fd, $server);
        $this->botRequest = static::wrapBotRequest($input);
        $this->botResponse = static::wrapBotResponse($this->botRequest);
        $this->botResponse->setShouldEndSession(false);
    }


    public static function wrapBotRequest(SwooleRequest $request) : BotRequest
    {
        $psr7Request = Psr7Request::loadFromSwooleRequest($request);
        // prepare duer os bot request
        $rawInput = $psr7Request->getBody()->getContents();
        $rawInput = str_replace("", "", $rawInput);
        $postData = json_decode($rawInput, true);
        return new BotRequest($postData);
    }

    public static function wrapBotResponse(BotRequest $request) : BotResponse
    {
        return new BotResponse(
            $request,
            $request->getSession(),
            $request->getNlu()
        );
    }

    /**
     * @return BotRequest
     */
    public function getBotRequest(): BotRequest
    {
        return $this->botRequest;
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
        $output =$this->botResponse->build([
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
        return $this->botRequest->getUserId() ?? '';
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
        return $this->botRequest->getUserInfo() ?? [];
    }

    /**
     * @param SwooleRequest $input   ignore
     * @return Message
     */
    protected function makeInputMessage($input): Message
    {
        if ($this->botRequest->isLaunchRequest()) {
            return new ConnectionEvt();
        }
        if ($this->botRequest->isSessionEndedRequest()) {
            return new QuitEvt();
        }

        return new Text($this->botRequest->getQuery());
    }

    public function fetchNLU(): ? NLU
    {
        if (isset($this->nlu)) {
            return $this->nlu;
        }
        $nlu = new NatureLanguageUnit();

        $userId = $this->fetchUserId();
        if (empty($userId)) {
            $nlu->setMatchedIntent(QuitInt::getContextName());

        } elseif ($this->botRequest->isLaunchRequest()) {
            $nlu->setMatchedIntent(HomeInt::class);

        } elseif ($this->botRequest->isSessionEndedRequest()) {
            $nlu->setMatchedIntent(QuitInt::class);
        } else {
            $nlu = $this->bootIntentNLU($nlu);
        }

        return $this->nlu = $nlu;
    }

    protected function bootIntentNLU(NLU $nlu) : NLU
    {
        $botNLU = $this->botRequest->getNlu();

        // matched nlu
        $matchedIntent = $botNLU->getIntentName();
        if (empty($matchedIntent)) {
            return $nlu;
        }

        // 默认模式下, 视作没有匹配到任何意图.
        if ($matchedIntent === DuerOSCommonIntents::COMMON_DEFAULT) {
            return $nlu;
        }

        $nlu->setMatchedIntent($botNLU);
        $intentData = $this->botRequest->getData()['request']['intents'];
        // possible intent and entities
        foreach ($intentData as $intentDatum) {
            $name = $intentDatum['name'] ?? '';
            if (empty($name)) {
                continue;
            }

            $nlu->addPossibleIntent($name, 0);
            $slots = $intentDatum['slots'] ?? [];
            if (!empty($slots)) {
                $entities = array_map(function($slot){
                    return $slot['values'] ?? $slot['value'] ?? null;
                }, $slots);
                $nlu->setIntentEntities($name, $entities);

                //todo duer os 的 confirm 先没有处理
            }
        }
        return $nlu;
    }

    public function fetchMessageId(): string
    {
        return $this->messageId = $this->botRequest->getLogId() ?? $this->generateMessageId();
    }

    public function fetchSessionId(): ? string
    {
        return $this->sessionId
                //todo bot-sdk 开发规范不够好.
            ?? $this->sessionId = $this->botRequest->getSession()->sessionId;
    }


    /**
     * @return BotResponse
     */
    public function getBotResponse(): BotResponse
    {
        return $this->botResponse;
    }

}