<?php
namespace Dittto\RequestLoggerBundle\Logger;

use Psr\Log\AbstractLogger;

class RequestLoggerPassthroughDecoratorTest extends \PHPUnit_Framework_TestCase
{
    private $requestLogger;
    private $otherLogger;

    public function setUp()
    {
        $this->requestLogger = new class extends AbstractLogger implements RetrievableLogsInterface {
            public $logs = [];
            public function log($level, $message, array $context = array()) {
                $this->logs[] = [$level, $message, $context];
            }
            public function getLogs(): array {
                return $this->logs;
            }
        };

        $this->otherLogger = new class extends AbstractLogger {
            public $logs = [];
            public function log($level, $message, array $context = array()) {
                $this->logs[] = [$level, $message, $context];
            }
        };
    }

    public function testAlertPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->alert('test_message', ['test_context']);

        $this->assertContains(['alert', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['alert', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testCriticalPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->critical('test_message', ['test_context']);

        $this->assertContains(['critical', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['critical', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testDebugPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->debug('test_message', ['test_context']);

        $this->assertContains(['debug', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['debug', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testEmergencyPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->emergency('test_message', ['test_context']);

        $this->assertContains(['emergency', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['emergency', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testErrorPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->error('test_message', ['test_context']);

        $this->assertContains(['error', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['error', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testInfoPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->info('test_message', ['test_context']);

        $this->assertContains(['info', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['info', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testLogPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->log('test_level', 'test_message', ['test_context']);

        $this->assertContains(['test_level', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['test_level', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testNoticePassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->notice('test_message', ['test_context']);

        $this->assertContains(['notice', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['notice', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testWarningPassedToBothLoggers()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->warning('test_message', ['test_context']);

        $this->assertContains(['warning', 'test_message', ['test_context']], $this->requestLogger->logs);
        $this->assertContains(['warning', 'test_message', ['test_context']], $this->otherLogger->logs);
    }

    public function testGettingLogs()
    {
        $decorator = new RequestLoggerPassthroughDecorator($this->requestLogger, $this->otherLogger);
        $decorator->warning('test_message', ['test_context']);

        $this->assertContains(['warning', 'test_message', ['test_context']], $decorator->getLogs());
    }
}