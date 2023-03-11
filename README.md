## This version is not work under construction
Version 0.0.1-alpha

## Simple Example

```php
Service::WhatsApp("sendText", [
    "Bearer" => "SEU TOKEN AQUI",
    "SecretKey" => "SEU SECRETKEY AQUI",
    "PublicToken" => "SEU PUBLICTOKEN AQUI", 
    "DeviceToken" => "SEU DEVICETOKEN AQUI",
    "body" => [
        "number" => "5531994359434",
        "text" => "🟢 https://apibrasil.com.br \n isso é um teste, caracteres especiais ? : !!! ççç  R$ 999,50"
    ]
]);
```