<?php
namespace Dittto\RequestLoggerBundle\Logger;

use Psr\Log\AbstractLogger;

class RequestLogger extends AbstractLogger implements RetrievableLogsInterface
{
    private $logs = [];

    public const LOGGED_LEVEL = 'level';
    public const LOGGED_MESSAGE = 'message';
    public const LOGGED_CONTEXT = 'context';

    public function log($level, $message, array $context = array())
    {
        $this->logs[] = [
            self::LOGGED_LEVEL => $level,
            self::LOGGED_MESSAGE => $message,
            self::LOGGED_CONTEXT => $context
        ];
    }

    public function getLogs():array
    {
        return $this->logs;
    }
}
