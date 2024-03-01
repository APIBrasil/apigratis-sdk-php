# SDK PHP <small> by API BRASIL</small>  🚀
### Conjunto de API, para desenvolvedores

_Transforme seus projetos em soluções inteligentes com nossa API. Com recursos como  API do WhatsApp, geolocalização, rastreamento de encomendas, verificação de CPF/CNPJ e mais, você pode criar soluções eficientes e funcionais. Comece agora._

[![latest stable version](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/v/stable.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
[![license mit](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/license.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
<a href="https://github.com/APIBrasil/apigratis-sdk-php/issues" target="_blank"><img alt="GitHub issues" src="https://img.shields.io/github/issues/APIBrasil/apigratis-sdk-php"></a>
<img alt="GitHub all releases" src="https://img.shields.io/github/downloads/APIBrasil/apigratis-sdk-php/total">
<a href="https://github.com/jhowbhz/apigratis-sdk-php/network" target="_blank"><img alt="GitHub forks" src="https://img.shields.io/github/forks/APIBrasil/apigratis-sdk-php"></a>
<a href="https://github.com/jhowbhz/apigratis-sdk-php/stargazers" target="_blank"><img alt="GitHub stars" src="https://img.shields.io/github/stars/APIBrasil/apigratis-sdk-php"></a>
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

## Canais de suporte (Comunidade)
[![WhatsApp Group](https://img.shields.io/badge/WhatsApp-Group-25D366?logo=whatsapp)](https://chat.whatsapp.com/EeAWALQb6Ga5oeTbG7DD2k)
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
| ✅ | AuthService                    | API Login e Logout                      |   ✅   | ✅                   | ✅                   |
| ✅ | ProfileService                 | API Dados do Usuario                    |   ✅   | ✅                   | ✅                   |
| ✅ | ServerService                  | API Lista Servidores                    |   ✅   | ✅                   | ✅                   |
| ✅ | DeviceService                  | API Criar, Editar Dispositivos          |   ✅   | ✅                   | ✅                   |
| ✅ | WhatsAppService                | API WhatsApp                            |   ✅   | ✅                   | ✅                   |
| ✅ | Receita Data CNPJ              | API Dados CNPJ Receita                  |   ✅   | ✅                   | ✅                   |
| ✅ | Receita Data CPF               | API Dados de CPF Serasa                 |   ✅   | ✅                   | ✅                   |
| ✅ | CorreiosService                | API Busca encomendas Correios Brazil    |   ✅   | ✅                   | ✅                   |
| ✅ | CEPLocation                    | API CEP Geolocation + IBGE Brazil       |   ✅   | ✅                   | ✅                   |
| ✅ | VehiclesService                | API Placa Dados                         |   ✅   | ✅                   | ✅                   |
| ✅ | FipeService                    | API Placa FIPE                          |   ✅   | ✅                   | ✅                   |
| ✅ | DDD Anatel                     | API Obtem DDD                           |   ✅   | ✅                   | ✅                   |
| ✅ | FeriadosBrasil                 | API Feriados Brasil                     |   ✅   | ✅                   | ✅                   |

## AuthService
Com essa API você poderá obter o Bearer Token

### Para fazer login válido por 1 ano
```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$login = Service::Auth("login", [
    "body" => [
        "email" => "jhondoe@gmail.com",
        "password" => "123456"
    ]
]);

var_dump($login);
die;
```

### Com essa API você poderá fazer logout, invalidando o Bearer Token

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$logout = Service::Auth("logout", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
]);

var_dump($logout);
die;
```

## DeviceService
Com essa API é possível criar, editar e deletar dispositivos

### Para criar um dispositivo
```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$store = Service::Device("store", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "SecretKey" => "f87eb607-a8cc-43ea-b439.....",
    "body" => [
        "type" => "cellphone",
        "device_name" => "zap1",
        "device_key" => "zapzap1",
        "device_ip" => "198.29.10.50",
        "server_search" => "341d6f36-b888....",
        "webhook_wh_message" => "",
        "webhook_wh_status" => ""
    ]
]);

var_dump($store);
die;
```

### Para atualizar um dispositivo

```php
require_once('vendor/autoload.php');
use ApiBrasil\Service;

$update = Service::Device("search", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "body" => [
        "type" => "cellphone",
        "search" => "82faab0a-24f4-4b8b-9926-455ea9b3cdb7",
        "server_search" => "a2c85262-f830-4b90-a8da-ff67b7a7ed6e",
        "device_name" => "zap2",
        "device_key" => "zapzap1",
        "device_ip" => "198.29.10.50",
        "webhook_wh_message" => "",
        "webhook_wh_status":""
    ]
]);

var_dump($update);
die;
```

### Para exibir um dispositivo
```php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$show = Service::Device("show", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "method" => "GET",
    "body" => [
        "search" => "82faab0a-24f4-4b8b-9926-455ea9b3cdb7",
    ]
]);

var_dump($show);
die;
```

## ServerService
Com essa API é possível listar todos os servidores ativos

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$servers = Service::Server([
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "method" => "GET",
]);

var_dump($servers);
die;
```

## ProfileService
Com essa API é possível listar detalhes do seu perfil

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$profile = Service::Profile([
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "method" => "GET",
]);

var_dump($profile);
die;
```

## PlanService
Com essa API é possível listar detalhes do seu plano

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$plan = Service::Plan([
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "method" => "GET",
]);

// ou obter o plano do usuario
$plan = Service::Plan("me", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "method" => "GET",
]);

var_dump($plan);
die;
```

## WhatsAppService
Com essa API é possível enviar mensagens de texto e outros

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$sendText = Service::WhatsApp("sendText", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "number" => "5531994359434",
        "text" => "🟢 Bem vindo ao APIBrasil"
    ]
]);

var_dump($sendText);
die;
```

## VehiclesService
Com essa API você obtem dados de caracteristicas de um veículo pela placa

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

## DDDBrasil
API para obter dados de todos os DDD's do Brasil, autorizados pela Anatel

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$ddd = Service::DDD("ddd", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "ddd" => "31",
    ]
]);

var_dump($ddd);
die;
```

## FeriadosBrasil
API para obter dados de todos os Feriados nacionais, estadual, municipal e facultativos
```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$feriados = Service::HoliDays("feriados", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "type" => "facultativo",
        "date" => "07/09",
        "year" => "2024"
    ]
]);

var_dump($feriados);
die;
```
## VehiclesService
API para obter dados da Tabela Fipe através da placa

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
Com essa API você pode obter dados de encomendas dos correios

### API para obter dados de encomendas através do rastreador
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

### API para obter dados de endereço através de um CEP

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
Obtenha dados de endereço através de um CNPJ de várias formas

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

## CEPService
Com essa API é possível obter dados de coordenadas LAT e LONG e código IBGE apenas com o CEP

- Obtenha Lat e Long por CEP
- Obtenha a lista de Cidades por UF
- Obtenha a lista de Bairros por Cidade
- Obtenha a lista de Estados

```php
<?php

require_once('vendor/autoload.php');
use ApiBrasil\Service;

$cep = Service::CEP("cep", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "cep" => "32146057",
    ]
]);

var_dump($cep);
die;
```
##  Observação
Você poderá obter mais endpoints na documentação de cada api, ou no link https://doc.apibrasil.io
