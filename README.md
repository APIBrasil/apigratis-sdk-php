# SDK PHP - APIGratis <small> by API BRASIL</small>  🚀
This package is free and can be used for API Brazil website functions

## Access platform and credentials
https://apigratis.com.br

[![latest stable version](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/v/stable.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
[![license mit](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/license.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
<a href="https://github.com/APIBrasil/apigratis-sdk-php/issues" target="_blank"><img alt="GitHub issues" src="https://img.shields.io/github/issues/APIBrasil/apigratis-sdk-php"></a>
<img alt="GitHub all releases" src="https://img.shields.io/github/downloads/APIBrasil/apigratis-sdk-php/total">
<a href="https://github.com/jhowbhz/apigratis-sdk-php/network" target="_blank"><img alt="GitHub forks" src="https://img.shields.io/github/forks/APIBrasil/apigratis-sdk-php"></a>
<a href="https://github.com/jhowbhz/apigratis-sdk-php/stargazers" target="_blank"><img alt="GitHub stars" src="https://img.shields.io/github/stars/APIBrasil/apigratis-sdk-php"></a>
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

## Our online channels
[![WhatsApp Group](https://img.shields.io/badge/WhatsApp-Group-25D366?logo=whatsapp)](https://chat.whatsapp.com/KsxrUGIPWvUBYAjI1ogaGs)
[![Telegram Group](https://img.shields.io/badge/Telegram-Group-32AFED?logo=telegram)](https://t.me/apigratisoficial)

## Status developing

| Up  | Services available            | Description       | Free    | Beta        | Stable   |
------|-------------------------------|-------------------|---------| ------------------------- | ------------------------- |
| ✅ | WhatsAppService                | Free in WhatsApp API.        |   ✅   | Loading                | Loading                    |
| ⌚ | CorreiosService                | API CEP or Tracker packages, correios Brazil.      |   ✅   | Loading                   | Loading                   |
| ⌚ | SinespService                  | API Plate get infos vehicle.       |   ✅   | Loading                   | Loading                   |
| ⌚ | FipeService                    | FIPE value the velhicle plate.       |   ✅   | Loading                   | Loading                   |

## Install package with composer
```bash
composer require jhowbhz/apigratis-sdk-php
```

## WhatsAppService
- Como enviar mensagens de texto

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$response = Service::WhatsApp("sendText", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "SecretKey" => "f87eb607-a8cc-43ea-b439...",
    "PublicToken" => "3f279a5c-bfbc-11ed-afa1...", 
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "number" => "5531994359434",
        "text" => "🟢 Bem vindo ao APIBrasil"
    ]
]);

var_dump($response);
die;
```
