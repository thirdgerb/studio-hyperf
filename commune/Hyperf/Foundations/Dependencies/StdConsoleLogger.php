<?php

/**
 * Class StdConsoleLogger
 * @package Commune\Hyperf\Foundations\Dependencies
 */

namespace Commune\Hyperf\Foundations\Dependencies;


use Commune\Chatbot\Contracts\ConsoleLogger;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LoggerTrait;

class StdConsoleLogger implements ConsoleLogger
{
    use LoggerTrait;

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * StdConsoleLogger constructor.
     * @param StdoutLoggerInterface $logger
     */
    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, strval($message), $context);
    }


}