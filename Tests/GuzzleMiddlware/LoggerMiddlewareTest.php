<?php
namespace Dittto\RequestLoggerBundle\GuzzleMiddleware;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\AbstractLogger;

class LoggerMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $logger;

    public function setUp()
    {
        $this->logger = new class extends AbstractLogger {
            public $logs = [];
            public function log($level, $message, array $context = array()) {
                $this->logs[] = [$level, $message, $context];
            }
        };
    }

    public function testLogOnRequestMade()
    {
        $middleware = new LoggerMiddleware();
        $action = $middleware->onRequest($this->logger);
        $handledAction = $action(function () {
            return new FulfilledPromise(new Response());
        });
        $handledAction(new Request('GET', 'test_uri'), []);

        Promise\queue()->run();

        $this->assertEquals('debug', $this->logger->logs[0][0]);
        $this->assertEquals('Request started', $this->logger->logs[0][1]);
        $this->assertEquals('test_uri', $this->logger->logs[0][2]['uri']);
    }

    public function testLogOnValidResponse()
    {
        $middleware = new LoggerMiddleware();
        $action = $middleware->onRequest($this->logger);
        $handledAction = $action(function () {
            return new FulfilledPromise(new Response());
        });
        $handledAction(new Request('GET', 'test_uri'), []);

        Promise\queue()->run();

        $this->assertEquals('notice', $this->logger->logs[1][0]);
        $this->assertEquals('Request success', $this->logger->logs[1][1]);
        $this->assertEquals('test_uri', $this->logger->logs[1][2]['uri']);
        $this->assertEquals(200, $this->logger->logs[1][2]['status_code']);
    }

    public function testLogOnFailedResponse()
    {
        $middleware = new LoggerMiddleware();
        $action = $middleware->onRequest($this->logger);
        $handledAction = $action(function () {
            return new Promise\RejectedPromise(new TransferException('test_message', 500));
        });
        $handledAction(new Request('GET', 'test_uri'), []);

        Promise\queue()->run();

        $this->assertEquals('error', $this->logger->logs[1][0]);
        $this->assertEquals('Request failed', $this->logger->logs[1][1]);
        $this->assertEquals('test_uri', $this->logger->logs[1][2]['uri']);
        $this->assertEquals(500, $this->logger->logs[1][2]['status_code']);
        $this->assertEquals('test_message', $this->logger->logs[1][2]['status_message']);
    }
}