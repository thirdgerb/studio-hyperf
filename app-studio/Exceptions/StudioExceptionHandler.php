<?php

/**
 * Class Handler
 * @package Commune\Studio\Exceptions
 */

namespace Commune\Studio\Exceptions;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;
use Commune\Chatbot\Framework\Exceptions\FatalExceptionHandler;

class StudioExceptionHandler extends FatalExceptionHandler
{

    public function logConversationalException(
        Conversation $conversation,
        \Throwable $e
    ): void
    {
        parent::logConversationalException($conversation, $e);
    }

    public function reportServiceStopException(
        StopServiceExceptionInterface $e
    ): void
    {
        parent::reportServiceStopException($e);
    }

    public function reportRuntimeException(
        RuntimeExceptionInterface $e
    ): void
    {
        parent::reportRuntimeException($e);
    }


}