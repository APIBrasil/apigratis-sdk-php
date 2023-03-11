## This version is not work under construction
Version 0.0.1-alpha

## Simple Example

```php
<?php
    require_once('vendor/autoload.php');
    use ApiBrasil\Service;

    Service::WhatsApp("sendText", [
        "Bearer" => "SEU TOKEN AQUI",
        "SecretKey" => "SEU SECRETKEY AQUI",
        "PublicToken" => "SEU PUBLICTOKEN AQUI", 
        "DeviceToken" => "SEU DEVICETOKEN AQUI",
        "body" => [
            "number" => "5531994359434",
            "text" => "🟢 Bem vindo ao APIBrasil"
        ]
    ]);
?>
```