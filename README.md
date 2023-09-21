# SDK PHP - APIGratis <small> by API BRASIL</small>  🚀
Conjunto de API, para desenvolvedores.

_Transforme seus projetos em soluções inteligentes com nossa API. Com recursos como  API do WhatsApp, geolocalização, rastreamento de encomendas, verificação de CPF/CNPJ e mais, você pode criar soluções eficientes e funcionais. Comece agora._

[![latest stable version](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/v/stable.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
[![license mit](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/license.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
<a href="https://github.com/APIBrasil/apigratis-sdk-php/issues" target="_blank"><img alt="GitHub issues" src="https://img.shields.io/github/issues/APIBrasil/apigratis-sdk-php"></a>
<img alt="GitHub all releases" src="https://img.shields.io/github/downloads/APIBrasil/apigratis-sdk-php/total">
<a href="https://github.com/jhowbhz/apigratis-sdk-php/network" target="_blank"><img alt="GitHub forks" src="https://img.shields.io/github/forks/APIBrasil/apigratis-sdk-php"></a>
<a href="https://github.com/jhowbhz/apigratis-sdk-php/stargazers" target="_blank"><img alt="GitHub stars" src="https://img.shields.io/github/stars/APIBrasil/apigratis-sdk-php"></a>
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

## Canais de suporte (Comunidade)
[![WhatsApp Group](https://img.shields.io/badge/WhatsApp-Group-25D366?logo=whatsapp)](https://chat.whatsapp.com/KsxrUGIPWvUBYAjI1ogaGs)
[![Telegram Group](https://img.shields.io/badge/Telegram-Group-32AFED?logo=telegram)](https://t.me/apigratisoficial)

## Obtenha suas credenciais
https://apibrasil.com.br

## Instalando pacote com o composer
```bash
composer require jhowbhz/apigratis-sdk-php
```

## Mais informações
https://packagist.org/packages/jhowbhz/apigratis-sdk-php

## Serviços de API disponíveis

| Up  | Services available            | Description       | Free    | Beta        | Stable   |
------|-------------------------------|-------------------|---------| ------------------------- | ------------------------- |
| ✅ | WhatsAppService                | API do WhatsApp Gratuita.               |   ✅   | ✅                   | ✅                   |
| ✅ | Receita Data CNPJ              | API Dados CNPJ Receita.                 |   ✅   | ✅                   | ✅                   |
| ✅ | Receita Data CPF               | API Dados de CPF Serasa.                |   ⌛   | ⌛                   | ⌛                   |
| ✅ | CorreiosService                | API Busca encomendas Correios Brazil.   |   ✅   | ✅                   | ✅                   |
| ✅ | CEPLocation                    | API CEP Geolocation + IBGE Brazil.      |   ✅   | ✅                   | ✅                   |
| ✅ | VehiclesService                | API Placa Dados.                        |   ✅   | ✅                   | ✅                   |
| ✅ | FipeService                    | API Placa FIPE.                         |   ✅   | ✅                   | ✅                   |

## WhatsAppService
- Como enviar mensagens de texto

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$response = Service::WhatsApp("sendText", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "number" => "5531994359434",
        "text" => "🟢 Bem vindo ao APIBrasil"
    ]
]);

var_dump($response);
die;
```
## VehiclesService
- Obtenha dados da placa de um veículo

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$dados = Service::Vehicles("dados", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "placa" => "HBM6603",
    ]
]);

var_dump($dados);
die;
```

## VehiclesService
- Obtenha dados da tabela fipe através da placade um veículo

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$fipe = Service::Vehicles("fipe", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "placa" => "HBM6603",
    ]
]);

var_dump($fipe);
die;
```

## CorreiosService
- Obtenha dados de uma encomenda através do tracker

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$rastreio = Service::Correios("rastreio", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "code" => "NL249695552BR",
    ]
]);

var_dump($rastreio);
die;
```

## CorreiosService
- Obtenha dados de endereço através de um CEP

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$address = Service::Correios("address", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "query" => "32146057",
    ]
]);

var_dump($address);
die;
```
## CNPJService
- Obtenha dados de endereço através de um CNPJ de várias formas
- Lista CNAES
- CPNJ por CEP *
- CNPJ por Estado *
- CNPJ por CNAE
- CNPJ por Capital Social *
- Lista sócios CNPJ *

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$cnpj = Service::CNPJ("cnpj", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "cnpj" => "44.959.669/0001-80",
    ]
]);

var_dump($cnpj);
die;
```

## CEPLocation
- Com essa API é possível obter dados de coordenadas LAT e LONG e código IBGE apenas com o CEP

- Obtenha Lat e Long por CEP
- Obtenha a lista de Cidades por UF
- Obtenha a lista de Bairros por Cidade
- Obtenha a lista de Estados

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$cnpj = Service::CEP("cep", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "cep" => "32146057",
    ]
]);

var_dump($cnpj);
die;
```
