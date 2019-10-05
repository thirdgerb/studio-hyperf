<?php


namespace Commune\Wechat\Servers;


use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Hyperf\Support\HttpBabel;
use Commune\Wechat\Contracts\MessageBabel;
use EasyWeChat\Kernel\Contracts\MessageInterface;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use Swoole\Server;
use Commune\Chatbot\App\Messages;
use Commune\Wechat\WechatComponent;
use Commune\Wechat\Messages\WechatEvent;
use Commune\Wechat\Constants\EventTypes;
use Commune\Wechat\Constants\MessageTypes;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Wechat\Messages as WechatMessages;
use EasyWeChat\Kernel\Messages as EasyWechatMessages;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Messages\Unsupported;
use EasyWeChat\OfficialAccount\Application as Wechat;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Commune\Chatbot\Framework\Exceptions\RequestException;
use Commune\Hyperf\Foundations\Requests\SwooleHttpMessageRequest;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 微信客户端的
 */
class OfficialAccountRequest extends SwooleHttpMessageRequest
{
    const WECHAT_MESSAGE_TYPE = 'wechat.msgType.';

    /*--------- cached ---------*/

    /**
     * @var Wechat
     */
    protected $wechat;

    /**
     * @var WechatComponent
     */
    protected $componentOption;

    /**
     * @var string
     */
    protected $userWechatId;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var bool
     */
    protected $valid;

    /**
     * @var MessageInterface|null
     */
    protected $output;

    public function __construct(
        HyperfBotOption $botOption,
        Server $server,
        SwooleRequest $request,
        SwooleResponse $response
    )
    {
        parent::__construct($botOption, null, $server, $request, $response);
    }

    public function getWechat() : Wechat
    {
        if (isset($this->wechat)) {
            return $this->wechat;
        }
        $this->wechat = $this->conversation->make(Wechat::class);

        // 注册自身返回值做唯一的 output
        try {

            $this->wechat->server->push(function() : ? MessageInterface {
                return $this->output;
            });

        } catch (\Exception $e) {
            throw new RequestException(__METHOD__, $e);
        }

        return $this->wechat;
    }

    public function getInput()
    {
        try {
            return $this->input
                ?? $this->input = $this->getWechat()->server->getMessage();

        } catch (\Exception $e){
            throw new RequestException(__METHOD__, $e);
        }
    }

    public function getScene(): ? string
    {
        return $this->getSwooleRequest()->get['scene'] ?? null;
    }

    public function getPlatformId(): string
    {
        return OfficialAccountServer::class;
    }

    public function getOption() : WechatComponent
    {
        return $this->componentOption
            ?? $this->componentOption = $this->conversation->make(WechatComponent::class);
    }


    public function fetchMessageId(): string
    {
        return $this->messageId
            ?? $this->messageId = $this->input['MsgId'] ?? $this->createUuId();
    }



    /*---------- user -----------*/

    public function fetchUserId(): string
    {
        return $this->getOpenId();
    }


    public function getOpenId() : string
    {
        if (isset($this->userWechatId)) {
            return $this->userWechatId;
        }

        $value = $this->getInput()['FromUserName'] ?? '';
        return $this->userWechatId = strval($value);
    }

    public function fetchUserName(): string
    {
        return $this->userName
            ?? (
                $this->userName = $this->fetchUserData()['nickname'] ?? 'guest'
            );
    }

    public function fetchUserData(): array
    {
        try {
            /**
             * @var CacheAdapter $cache
             */
            $cache = $this->conversation->make(CacheAdapter::class);

            $id = $this->fetchUserId();
            $key = "chatbot:wechat:user:$id";

            if ($cache->has($key)) {
                $data = $cache->get($key);
                if (is_string($data)) {
                    $array = unserialize($data);
                    if (is_array($array)) {
                        return $array;
                    }
                }
            }

            $openId = $this->getOpenId();

            if (empty($openId)) {
                $this->logger->error("empty wechat openId: ". json_encode($this->input));
                return [];
            }

            $user = $this->getWechat()->user->get($this->getOpenId());
            $serialized = serialize($user);
            $cache->set($key, $serialized, 3600);

            return $user;

        } catch (\Exception $e) {
            $this->logger->error($e);
            return [];

        }
    }


    /*---------- input -----------*/

    protected function makeInputMessage($input): Message
    {
        $msgType = $this->input['MsgType'] ?? '';

        // 允许通过依赖注入绑定的方式来生成 message
        // 十有八九会被自己忘记..
        $bindId = self::WECHAT_MESSAGE_TYPE . $msgType;
        if ($this->conversation->bound($bindId)) {
            return $this->conversation->make($bindId);
        }

        // 系统默认的 message 处理.
        switch ($msgType) {

            case MessageTypes::TEXT :
                return new Messages\Text($this->input['Content'] ?? '');

            case MessageTypes::EVENT:
                $event = $this->input['Event'] ?? '';
                if ( $event === EventTypes::SUBSCRIBE) {
                    return new Messages\Events\ConnectionEvt();
                }

                return new WechatEvent($this->input['Event'] ?? '');

            case MessageTypes::VOICE :
                $id = $this->input['MediaId'] ?? '';
                $voice = new WechatMessages\WechatAudio($id);

                $recognition = $this->input['Recognition'] ?? null;
                // 加入语音识别.
                if (isset($recognition)) {
                    return new Messages\Recognitions\VoiceRecognition($voice, $recognition);
                }

                return $voice;

            case MessageTypes::IMAGE :
                $id = $this->input['MediaId'] ?? '';
                $url = $this->input['PicUrl'] ?? '';
                return new WechatMessages\WechatImage($id, $url);

            default :
                return new Unsupported();
        }
    }

    /*------- 验证 -------*/

    /**
     * @var int
     */
    protected $errCode = 400;

    /**
     * @var string
     */
    protected $errReason = '';

    protected function doValidate(): bool
    {
        try {

            $openId = $this->getOpenId();
            if (empty($openId)) {
                $this->logger->warning('request validate fail, openid is missing');
                return false;
            }

            $this->getWechat()->server->validate();
            return true;

        } catch (BadRequestException $e) {
            $this->errCode = $e->getCode();
            $this->errReason = $e->getMessage();

            $this->logger->warning('request validate failed', [
                'errCode' => $this->errCode,
                'errMsg' => $this->errReason,
            ]);

            return false;
        }
    }

    public function sendRejectResponse(): void
    {
        // 让 overtrue wechat 自己去处理.
        $this->flushResponse();
    }


    /**
     * @param ConversationMessage[] $messages
     */
    protected function renderChatMessages(array $messages): void
    {
        try {

            // 由于微信公众号现在不允许单个请求多个回复, 所以只好区别对待.
            if (count($messages) === 1) {
                $this->renderSingleMessage(current($messages));
            } else {
                $this->renderMultipleMessages($messages);
            }

        } catch (GuzzleException $e) {
            throw new RequestException(__METHOD__, $e);

        } catch (\Throwable $e) {
            throw new RequestException(__METHOD__, $e);
        }

    }

    /**
     * @param ConversationMessage $message
     * @throws GuzzleException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function renderSingleMessage(ConversationMessage $message) : void
    {
        // 如果实现了 message babel
        if ($this->conversation->has(MessageBabel::class)) {
            $babel = $this->conversation->get(MessageBabel::class);
            $this->output = $babel->transform($message->getMessage());
            return;
        }

        $msg = $message->getMessage();

        if ($msg instanceof WechatMessages\TemplateMessage) {
            $this->sendWechatTemplateMessage($msg);

        } elseif ($msg instanceof VerboseMsg) {
            $this->output = new EasyWechatMessages\Text($msg->getText());

        } elseif ($msg instanceof WechatMessages\WechatMessage) {
            $this->output = $msg->toEasyWechatMessage();

        } else {
            $this->logger->error(__METHOD__ . ' message not supported', [
                'message' => $message->toArray()
            ]);
        }
    }

    /**
     * @param WechatMessages\TemplateMessage $message
     * @throws GuzzleException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function sendWechatTemplateMessage(WechatMessages\TemplateMessage $message) : void
    {
        $this->getWechat()
            ->template_message
            ->send($message->getTemplateData());
    }


    /**
     * @param ConversationMessage[] $messages
     * @throws GuzzleException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function renderMultipleMessages(array $messages) : void
    {
        /**
         * @var Message[] $msgs
         */
        $msgs = array_map(function(ConversationMessage $message) : Message {
            return $message->getMessage();
        }, $messages);

        $text = [];

        foreach ($msgs as $message) {
            if ($message instanceof VerboseMsg) {
                $text[] = $message->getText();

            // 微信公众号不允许多个回复消息, 因此多个消息时, 应该用发送模板消息的方式来解决.
            } elseif ($message instanceof WechatMessages\TemplateMessage) {
                $this->sendWechatTemplateMessage($message);

            // 暂时不支持的消息类型.
            } else {
                $this->logger->warning(
                    __METHOD__ . ' not support message',
                    [
                        'message' => $message->toArray()
                    ]
                );
            }

        }
        $this->output = new EasyWechatMessages\Text(implode("\n\n", $text));
    }

    protected function flushResponse(): void
    {
        try {
            $response = $this->getWechat()->server->serve();
            HttpBabel::sendResponseFromSymfonyToSwoole($response, $this->getSwooleResponse(), false);

        } catch (\Exception $e) {
            $this->logger->error($e);
            throw new RequestException(__METHOD__, $e);
        }
    }

}