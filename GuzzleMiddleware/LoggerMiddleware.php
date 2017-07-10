<?php
namespace Dittto\RequestLoggerBundle\GuzzleMiddleware;

use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Promise\RejectedPromise;
use Psr\Http\Message\{
    RequestInterface, ResponseInterface
};
use Psr\Log\LoggerInterface;

class LoggerMiddleware
{
    public function onRequest(LoggerInterface $logger)
    {
        return function (callable $handler) use ($logger) {
            return function (RequestInterface $request, array $options) use ($handler, $logger) {
                $startTime = microtime(true);
                $logger->debug('Request started', [
                    LoggedVars::URI => (string) $request->getUri(),
                    LoggedVars::CURRENT_TIME => $startTime,
                ]);

                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $logger, $startTime) {
                        $endTime = microtime(true);
                        $logger->notice('Request success', [
                            LoggedVars::URI => (string) $request->getUri(),
                            LoggedVars::CURRENT_TIME => $endTime,
                            LoggedVars::STATUS_CODE => $response->getStatusCode(),
                            LoggedVars::TIME_TAKEN => $endTime - $startTime,
                        ]);

                        return $response;
                    },
                    function (TransferException $e) use ($request, $logger, $startTime) {
                        $endTime = microtime(true);
                        $logger->error('Request failed', [
                            LoggedVars::URI => (string) $request->getUri(),
                            LoggedVars::CURRENT_TIME => $endTime,
                            LoggedVars::STATUS_CODE => $e->getCode(),
                            LoggedVars::STATUS_MESSAGE => $e->getMessage(),
                            LoggedVars::TIME_TAKEN => $endTime - $startTime,
                        ]);

                        return new RejectedPromise($e);
                    }
                );
            };
        };
    }
}
