<?php
namespace Dittto\RequestLoggerBundle\GuzzleMiddleware;

class LoggedVars
{
    public const CURRENT_TIME = 'current_timestamp';
    public const STATUS_CODE = 'status_code';
    public const STATUS_MESSAGE = 'status_message';
    public const TIME_TAKEN = 'time_taken_in_s';
    public const URI = 'uri';
}
