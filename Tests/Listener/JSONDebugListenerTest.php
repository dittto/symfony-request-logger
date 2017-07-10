<?php
namespace Dittto\RequestLoggerBundle\Listener;

use Dittto\RequestLoggerBundle\Logger\RetrievableLogsInterface;
use Psr\Log\AbstractLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class JSONDebugListenerTest extends \PHPUnit_Framework_TestCase
{
    private $logger;

    public function setUp()
    {
        $this->logger = new class extends AbstractLogger implements RetrievableLogsInterface {
            public $logs = [];
            public function log($level, $message, array $context = array()) {}
            public function getLogs(): array {
                return $this->logs;
            }
        };
    }

    public function testNoChangeIfNotDebugMode()
    {
        $kernel = new class implements HttpKernelInterface {
            public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
        };
        $request = new Request();
        $response = new Response(json_encode([]));

        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $listener = new JSONDebugListener($this->logger, false);
        $listener->onKernelResponse($event);

        $this->assertEquals([], json_decode($response->getContent(), true));
    }

    public function testNoChangeIfNotJSONResponse()
    {
        $kernel = new class implements HttpKernelInterface {
            public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
        };
        $request = new Request();
        $response = new Response(json_encode(['test' => true]), 200, ['content-type' => 'test_type']);

        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $listener = new JSONDebugListener($this->logger, true);
        $listener->onKernelResponse($event);

        $this->assertEquals(['test' => true], json_decode($response->getContent(), true));
    }

    public function testNoChangeIfNotValidStatusCode()
    {
        $kernel = new class implements HttpKernelInterface {
            public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
        };
        $request = new Request();
        $response = new Response(json_encode(['test' => true]), 404, ['content-type' => 'application/json']);

        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $listener = new JSONDebugListener($this->logger, true);
        $listener->onKernelResponse($event);

        $this->assertEquals(['test' => true], json_decode($response->getContent(), true));
    }

    public function testNoChangeIfNotValidJSONBody()
    {
        $kernel = new class implements HttpKernelInterface {
            public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
        };
        $request = new Request();
        $response = new Response('not_json', 200, ['content-type' => 'application/json']);

        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $listener = new JSONDebugListener($this->logger, true);
        $listener->onKernelResponse($event);

        $this->assertEquals('not_json', $response->getContent());
    }

    public function testDebugInfoAppendedIfAllowed()
    {
        $this->logger->logs = ['test_logged_data'];

        $kernel = new class implements HttpKernelInterface {
            public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {}
        };
        $request = new Request();
        $response = new Response(json_encode(['test' => true]), 200, ['content-type' => 'application/json']);

        $event = new FilterResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);

        $listener = new JSONDebugListener($this->logger, true);
        $listener->onKernelResponse($event);

        $this->assertEquals(['test' => true, 'debug' => ['test_logged_data']], json_decode($response->getContent(), true));
    }
}