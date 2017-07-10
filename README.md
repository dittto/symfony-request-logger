# Symfony request logger

## What is it?

Any apps we write will, quite often, have cause to make external calls to either our own, or third-party, APIs. This Symfony bundle makes it easy to track how long these requests are taking and return the result via either monolog and/or an additional JSON object that's appended to your output when Symfony's in debug mode.  

## How to set it up

```yaml
monolog:
    channels:
        [ "external_request" ]
```

```yaml
services:
    dittto.request_logger.monolog_channel:
        alias: 'monolog.logger.external_request'
```


```yaml
services:
    dittto.request_logger.debug_listener:
        class: Dittto\RequestLoggerBundle\Listener\JSONDebugListener
        arguments: [ '@dittto.request_logger', '%kernel.debug%' ]
        tags:
            - { name: kernel.event_listener, event: kernel.response, method: 'onKernelResponse' }
```