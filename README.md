# This version is not work under construction
Version 0.0.1-alpha

## Simples Examples

- Como enviar mensagens de texto

```sendText.php```

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$response = Service::WhatsApp("sendText", [
    "Bearer" => "SEU TOKEN AQUI",
    "SecretKey" => "SEU SECRETKEY AQUI",
    "PublicToken" => "SEU PUBLICTOKEN AQUI", 
    "DeviceToken" => "SEU DEVICETOKEN AQUI",
    "body" => [
        "number" => "5531994359434",
        "text" => "🟢 Bem vindo ao APIBrasil"
    ]
]);

var_dump($response);
die;

?>
```
