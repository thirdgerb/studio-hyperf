<?php


namespace Commune\Platform\WebApi\SessionPipes;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Commune\Platform\WebApi\Libraries\AbstractAction;
use Commune\Platform\WebApi\Servers\ApiRequest;

class ApiActionMatcher implements SessionPipe
{
    /**
     * @var ApiRequest
     */
    protected $apiRequest;

    /**
     * ApiActionMatcher constructor.
     * @param ApiRequest $apiRequest
     */
    public function __construct(ApiRequest $apiRequest)
    {
        $this->apiRequest = $apiRequest;
    }


    public function handle(Session $session, \Closure $next): Session
    {
        $session->beSneak();

        $action = $this->apiRequest->getScene();
        $input = $this->apiRequest->getInput();

        /**
         * @var AbstractAction $handler
         */
        $conversation = $session->conversation;
        $handler = $conversation->make($action);

        $apiResult = $handler->handle($session, $input);

        $this->apiRequest->withResponse($apiResult);

        return $next($session);
    }


}