# Symfony request logger

## What is it?

Any apps we write will, quite often, have cause to make external calls to either our own, or third-party, APIs. This Symfony bundle makes it easy to track how long these requests are taking and return the result via either monolog and/or an additional JSON object that's appended to your output when Symfony's in debug mode.

It works by adding extra middleware to Guzzle so we can track when a request is made and either succeeds or fails, and then storing these logs for later output.

## How to set it up

### Basic setup

There are a few different options for using this request logger. The most simple is to log all requests made via Guzzle, and then recall the logs later.

To do this, add / update the following services. If you've already got a Guzzle service, then update it as required:

```yaml
services:
    dittto.request_logger.alias:
        alias: 'dittto.request_logger'

    http_client:
        class: GuzzleHttp\Client
        arguments:
          - handler: '@http_client.handlerstack'
            connect_timeout: 5
            timeout: 5

    http_client.handlerstack:
        class: GuzzleHttp\HandlerStack
        factory: [ GuzzleHttp\HandlerStack, 'create' ]
        calls:
          - [ 'push', [ '@dittto.request_logger.middleware.request' ] ]
```

What does this do then? The first service is to define which version of the request logger we're going to use. `dittto.request_logger` is the most basic form of `LoggerInterface` that simply stores all requests for later.

After that, we create a Guzzle client, and then make sure our middleware has been added to it, so we can store our API request attempts.

To use this, you'd probably do something like:

```yaml
services:
    test_controller:
            class: AppBundle\Controller\TestController
            arguments: [ '@http_client', '@dittto.request_logger.alias' ]
```

```php
<?php
class TestController {
    private $client;
    private $logger;
    
    public function __construct(ClientInterface $client, RetrievableLogsInterface $logger) {
        $this->client = $client;
        $this->logger = $logger;
    }
    
    public function index()
    {
        $this->client->request('GET', 'https://api-path');
        
        var_dump($this->logger->getLogs());
    }
}
```

### With monolog

Above shows how to use it and deal with the result yourself. Realistically, you're going to want to automatically output the request logger one way or another.

We'll use monolog to output our logged messages this time. To begin with, let's create a unique monolog channel for all of these messages:

```yaml
monolog:
    channels:
        [ "external_request" ]
```

Next, create the following services. If you've already defined Guzzle then just pick and choose the settings you wish to use:

```yaml
services:
    dittto.request_logger.alias:
        alias: 'dittto.request_logger.passthrough'

    dittto.request_logger.monolog_channel:
        alias: 'monolog.logger.external_request'

    http_client:
        class: GuzzleHttp\Client
        arguments:
          - handler: '@http_client.handlerstack'
            connect_timeout: 5
            timeout: 5

    http_client.handlerstack:
        class: GuzzleHttp\HandlerStack
        factory: [ GuzzleHttp\HandlerStack, 'create' ]
        calls:
          - [ 'push', [ '@dittto.request_logger.middleware.request' ] ]
```

Note that the changes this time are to the first two services. The alias now references `dittto.request_logger.passthrough` which allows us to save our logged messages to both our request logger, and monolog.

The second service is an alias to the new monolog channel we created above.

Now if you run your code, both monolog will save changes and the request logger has them available via `getLogs()`.

If you're just going to use monolog for recording those messages, then you can use just the guzzle middleware instead of everything else. This is done using the following services:
 
```yaml
services:
    http_client:
        class: GuzzleHttp\Client
        arguments:
          - handler: '@http_client.handlerstack'
            connect_timeout: 5
            timeout: 5

    http_client.handlerstack:
        class: GuzzleHttp\HandlerStack
        factory: [ GuzzleHttp\HandlerStack, 'create' ]
        calls:
          - [ 'push', [ '@dittto.request_logger.middleware.request.monolog_only' ] ]

    dittto.request_logger.middleware.request.monolog_only:
        class: Closure
        factory: [ '@dittto.request_logger.middleware', 'onRequest' ]
        arguments: [ '@monolog.logger.external_request' ]
```

### JSON debug object 

If you're building, maintaining, and debugging a lot of JSON APIs, then you can have this plugin automatically add your request logs to your JSON output when Symfony is in debug mode. 

Add the following service to your app to capture valid responses and update your JSON:

```yaml
services:
    dittto.request_logger.debug_listener:
        class: Dittto\RequestLoggerBundle\Listener\JSONDebugListener
        arguments: [ '@dittto.request_logger', '%kernel.debug%' ]
        tags:
            - { name: kernel.event_listener, event: kernel.response, method: 'onKernelResponse' }
```