<?php
namespace Dittto\RequestLoggerBundle\Logger;

class RequestLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogStored()
    {
        $logger = new RequestLogger();
        $logger->log('test_level', 'test_message', ['test_context' => true]);

        $this->assertCount(1, $logger->getLogs());
        $this->assertEquals('test_level', $logger->getLogs()[0][RequestLogger::LOGGED_LEVEL]);
        $this->assertEquals('test_message', $logger->getLogs()[0][RequestLogger::LOGGED_MESSAGE]);
        $this->assertEquals(['test_context' => true], $logger->getLogs()[0][RequestLogger::LOGGED_CONTEXT]);
    }
}