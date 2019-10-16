<?php


namespace Commune\Platform\WebApi\Libraries;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Platform\WebApi\WebApiComponent;

/**
 * api action
 */
abstract class AbstractAction
{
    public function handle(Session $session, array $input) : ApiResult
    {
        $err = $this->validateInput($input);

        if (isset($err)) {
            return new ApiResult([], WebApiComponent::CODE_BAD_REQUEST);
        }

        return $this->doHandle($session, $input);
    }


    abstract public function validateInput(array $input) : ? string;

    abstract public function doHandle(Session $session, array $input) : ApiResult;
}