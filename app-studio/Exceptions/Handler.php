<?php

/**
 * Class Handler
 * @package Commune\Studio\Exceptions
 */

namespace Commune\Studio\Exceptions;


use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Psr\Log\LoggerInterface;

class Handler implements ExceptionHandler
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Handler constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function reportServiceStopException(
        string $method,
        StopServiceExceptionInterface $e
    ): void
    {
        $this->logger->critical($method . ' '. $e);
    }

    public function reportRuntimeException(
        string $method,
        RuntimeExceptionInterface $e
    ): void
    {
        $this->logger->error($method . ' '. $e);
    }


}