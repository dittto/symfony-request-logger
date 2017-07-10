<?php
namespace Dittto\RequestLoggerBundle\Logger;

use Psr\Log\LoggerInterface;

class RequestLoggerPassthroughDecorator implements RetrievableLogsInterface
{
    private $requestLogger;
    private $otherLogger;

    public function __construct(RetrievableLogsInterface $requestLogger, LoggerInterface $otherLogger)
    {
        $this->requestLogger = $requestLogger;
        $this->otherLogger = $otherLogger;
    }

    public function alert($message, array $context = array())
    {
        $this->requestLogger->alert($message, $context);
        $this->otherLogger->alert($message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->requestLogger->critical($message, $context);
        $this->otherLogger->critical($message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->requestLogger->debug($message, $context);
        $this->otherLogger->debug($message, $context);
    }

    public function emergency($message, array $context = array())
    {
        $this->requestLogger->emergency($message, $context);
        $this->otherLogger->emergency($message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->requestLogger->error($message, $context);
        $this->otherLogger->error($message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->requestLogger->info($message, $context);
        $this->otherLogger->info($message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        $this->requestLogger->log($level, $message, $context);
        $this->otherLogger->log($level, $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->requestLogger->notice($message, $context);
        $this->otherLogger->notice($message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->requestLogger->warning($message, $context);
        $this->otherLogger->warning($message, $context);
    }

    public function getLogs(): array
    {
        return $this->requestLogger->getLogs();
    }
}