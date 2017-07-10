# Symfony request logger


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