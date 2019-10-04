<?php


namespace Commune\Hyperf\Foundations\Requests;

use Commune\Hyperf\Foundations\Contracts\SwooleHttpMsgReq;
use Commune\Hyperf\Foundations\Contracts\SwooleMsgReq;
use Commune\Hyperf\Foundations\Options\HyperfBotOption;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Server;

abstract class SwooleHttpMessageRequest extends AbstractMessageRequest implements SwooleHttpMsgReq
{
    /**
     * @var SwooleRequest
     */
    protected $request;

    /**
     * @var SwooleResponse
     */
    protected $response;

    public function __construct(
        HyperfBotOption $botOption,
        $input,
        Server $server,
        SwooleRequest $request,
        SwooleResponse $response
    )
    {
        $this->request = $request;
        $this->response = $response;
        parent::__construct($botOption, $input, $request->fd, $server);
    }

    protected function onBindConversation() : void
    {
        $this->conversation->share(self::class, $this);
        $this->conversation->share(SwooleHttpMsgReq::class, $this);
        $this->conversation->share(SwooleMsgReq::class, $this);

        $this->conversation->share(Server::class, $this->getServer());
        $this->conversation->share(SwooleRequest::class, $this->request);
        $this->conversation->share(SwooleResponse::class, $this->response);
    }


    /**
     * @return SwooleRequest
     */
    public function getSwooleRequest(): SwooleRequest
    {
        return $this->request;
    }

    /**
     * @return SwooleResponse
     */
    public function getSwooleResponse(): SwooleResponse
    {
        return $this->response;
    }

    public function sendFailureResponse(): void
    {
        $response = $this->getSwooleResponse();
        $response->status(500);
    }


}