<?php
namespace Dittto\RequestLoggerBundle\Listener;

use Dittto\RequestLoggerBundle\Logger\RetrievableLogsInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class JSONDebugListener
{
    private $logger;
    private $isDebug;
    private $validStatusCodes;

    private const CONTENT_TYPE_HEADER = 'content-type';
    private const CONTENT_TYPE_FOR_JSON = 'application/json';
    private const DEFAULT_VALID_STATUS_CODES = [200];
    private const OUTPUT_OBJECT_NAME = 'debug';

    public function __construct(RetrievableLogsInterface $logger, bool $isDebug = false, array $validStatusCodes = self::DEFAULT_VALID_STATUS_CODES)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
        $this->validStatusCodes = $validStatusCodes;
    }

    public function onKernelResponse(FilterResponseEvent $event):void
    {
        if (!$this->isDebug) {
            return;
        }

        $response = $event->getResponse();
        if ($response->headers->get(self::CONTENT_TYPE_HEADER) !== self::CONTENT_TYPE_FOR_JSON || !in_array($response->getStatusCode(), $this->validStatusCodes)) {
            return;
        }

        $content = $response->getContent();
        $data = json_decode($content, true);
        if ($data === null) {
            return;
        }

        $data[self::OUTPUT_OBJECT_NAME] = $this->logger->getLogs();

        $response->setContent(json_encode($data));
        $event->setResponse($response);
    }
}
