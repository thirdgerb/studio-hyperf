<?php

/**
 * Class StdConsoleLogger
 * @package Commune\Hyperf\Foundations\Dependencies
 */

namespace Commune\Hyperf\Foundations\Drivers;


use Commune\Chatbot\Contracts\ConsoleLogger;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

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
        if (CHATBOT_DEBUG === false && $level == LogLevel::DEBUG) {
            return;
        }
        $this->logger->log($level, strval($message), $context);
    }


}