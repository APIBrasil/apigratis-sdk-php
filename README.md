# SDK PHP - APIGratis by API BRASIL 🚀

SDK oficial PHP da plataforma [APIBrasil](https://apibrasil.com.br) — WhatsApp, SMS, consultas de CPF/CNPJ, veículos, CEP, correios, pagamentos PIX/boleto e muito mais.

[![latest stable version](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/v/stable.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
[![license mit](https://poser.pugx.org/jhowbhz/apigratis-sdk-php/license.svg)](https://packagist.org/packages/jhowbhz/apigratis-sdk-php)
<a href="https://github.com/APIBrasil/apigratis-sdk-php/issues" target="_blank"><img alt="GitHub issues" src="https://img.shields.io/github/issues/APIBrasil/apigratis-sdk-php"></a>
<a href="https://github.com/jhowbhz/apigratis-sdk-php/network" target="_blank"><img alt="GitHub forks" src="https://img.shields.io/github/forks/APIBrasil/apigratis-sdk-php"></a>
<a href="https://github.com/jhowbhz/apigratis-sdk-php/stargazers" target="_blank"><img alt="GitHub stars" src="https://img.shields.io/github/stars/APIBrasil/apigratis-sdk-php"></a>
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg?style=flat-square)](https://php.net/)

## Canais de suporte (Comunidade)

[![WhatsApp Group](https://img.shields.io/badge/WhatsApp-Group-25D366?logo=whatsapp)](https://chat.whatsapp.com/EeAWALQb6Ga5oeTbG7DD2k)
[![Telegram Group](https://img.shields.io/badge/Telegram-Group-32AFED?logo=telegram)](https://t.me/apigratisoficial)

## Instalação

```bash
composer require jhowbhz/apigratis-sdk-php
```

Requer **PHP >= 8.0**. Usa Guzzle quando disponível e cai automaticamente para
cURL — a camada de transporte é plugável.

Obtenha suas credenciais em https://apibrasil.com.br

## Começando

```php
<?php

require_once 'vendor/autoload.php';

use ApiBrasil\ApiBrasil;

$api = new ApiBrasil([
    'bearerToken' => getenv('APIBRASIL_BEARER_TOKEN'), // JWT do login
    'deviceToken' => getenv('APIBRASIL_DEVICE_TOKEN'), // device dos serviços device-based
]);

// WhatsApp
$api->whatsapp->sendText(['number' => '5511999999999', 'text' => 'Olá! 👋']);

// Consulta CNPJ (por créditos)
$empresa = $api->consulta->cnpj(['cnpj' => '00000000000000']);
print_r($empresa['data']);
```

As credenciais também podem vir só do ambiente — `new ApiBrasil()` lê automaticamente
`APIBRASIL_BEARER_TOKEN`, `APIBRASIL_DEVICE_TOKEN`, `APIBRASIL_SECRET_KEY` e `APIBRASIL_BASE_URL`.

Todas as respostas são devolvidas como **array associativo** já decodificado.

Também é possível autenticar por email/senha — o token retornado fica guardado no cliente:

```php
$api = new ApiBrasil();
$api->auth->login(['email' => 'voce@empresa.com.br', 'password' => '******']);

// contas com 2FA:
$session = $api->auth->login(['email' => $email, 'password' => $password]);
if (!empty($session['requires_2fa'])) {
    $api->auth->send2fa(['challenge' => $session['challenge'], 'method' => 'email']);
    $api->auth->verify2fa(['challenge' => $session['challenge'], 'code' => '000000']);
}
```

## Como a plataforma funciona

A API Brasil tem duas famílias de serviços:

| Família          | Autenticação                                   | Exemplos                                                                    |
| ---------------- | ---------------------------------------------- | --------------------------------------------------------------------------- |
| **Device-based** | `Authorization: Bearer` + header `DeviceToken` | WhatsApp, SMS, veículos, CEP, correios, DDD, feriados, tradução, clima, OCR |
| **Por créditos** | apenas `Authorization: Bearer` (debita saldo)  | `consulta->cpf`, `consulta->cnpj`, `consulta->veiculos`, Serasa, CNH, telefone |

Para os serviços device-based, crie um device com a `SecretKey` da API desejada (painel APIBrasil) e use o `device_token` retornado:

```php
$device = $api->devices->store(
    ['device_name' => 'meu-bot', 'type' => 'server'],
    ['secretKey' => 'SUA_SECRET_KEY']
);

$api->setDeviceToken($device['device']['device_token']);
```

## Serviços disponíveis

| Módulo                                                            | Descrição                                                                                                    |
| ----------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `$api->whatsapp`                                                  | WhatsApp: `start`, `qrcode`, `sendText`, `sendFile`, `sendAudio`, `sendVideo`, fila (`queue`)...              |
| `$api->evolution`                                                 | Evolution API: `request($controller, $action, $body)`                                                        |
| `$api->whatsmeow`                                                 | WhatsMeow: `request($action, $body)`                                                                         |
| `$api->sms`                                                       | SMS device-based (`send`) e por créditos (`sendWithCredits`)                                                 |
| `$api->dados`                                                     | Dados cadastrais device-based (`cpf`, `cnpj`)                                                                |
| `$api->vehicles`                                                  | Veículos por placa (`dados`, `fipe`, `consultaFipe`)                                                         |
| `$api->fipe`                                                      | Tabela FIPE (`request($action, $body)`)                                                                      |
| `$api->correios`                                                  | Correios (`rastreio`, `request`)                                                                             |
| `$api->cep`                                                       | CEP + geolocalização (`cep`, `request`)                                                                      |
| `$api->geolocation` / `$api->geomatrix`                           | Geolocalização e matriz de distâncias                                                                        |
| `$api->recognize`                                                 | OCR / Google Vision                                                                                          |
| `$api->ddd` / `$api->holidays` / `$api->translate` / `$api->weather` | DDD, feriados, tradução, clima                                                                             |
| `$api->databaseIp`                                                | GeoIP (`ip`)                                                                                                 |
| `$api->consulta`                                                  | Consultas por créditos: `cpf`, `cnpj`, `cnh`, `cep`, `veiculos`, `telefone`, `generic($service, $body)`      |
| `$api->ura` / `$api->chipVirtual`                                 | URA reversa e chip virtual                                                                                   |
| `$api->bulk`                                                      | Execução em lote (`direct`, `queue`)                                                                         |
| `$api->auth`                                                      | Login, 2FA, cadastro, recuperação de senha, perfil                                                           |
| `$api->devices`                                                   | CRUD de devices                                                                                              |
| `$api->catalog`                                                   | Catálogo de APIs, planos, documentações, servidores                                                          |
| `$api->account`                                                   | Saldo, faturas, notificações, tickets                                                                        |
| `$api->payments`                                                  | Recargas e pagamentos PIX/boleto/cartão (Santander, Inter, Mercado Pago, Sicoob)                             |
| `$api->ipWhitelist` / `$api->bearerRateLimit`                     | Segurança da conta                                                                                           |
| `$api->reports`                                                   | Relatórios e dashboard de consumo                                                                            |

### WhatsApp

```php
// iniciar sessão e obter QR Code
$api->whatsapp->start(['webhook_wh_message' => 'https://seu-webhook.com/mensagens']);

$qr = $api->whatsapp->qrcode();
echo $qr['response']['qrcode']; // data URI base64

// envios
$api->whatsapp->sendText(['number' => '5511999999999', 'text' => 'Olá!']);
$api->whatsapp->sendFile(['number' => '5511999999999', 'path' => 'https://exemplo.com/nota.pdf']);
$api->whatsapp->sendAudio(['number' => '5511999999999', 'path' => 'https://exemplo.com/audio.mp3']);

// qualquer action da documentação, inclusive via fila
$api->whatsapp->request('sendLocation', ['number' => '5511999999999', 'lat' => -23.5, 'lng' => -46.6]);
$api->whatsapp->queue('sendText', ['number' => '5511999999999', 'text' => 'assíncrono 🚀']);
```

### Consultas por créditos

```php
// CPF / CNPJ
$cpf = $api->consulta->cpf(['cpf' => '00000000000']);
$socios = $api->consulta->cnpj(['cnpj' => '00000000000000', 'tipo' => 'lista-socios']);

// veicular
$veiculo = $api->consulta->veiculos(['placa' => 'ABC1234']);

// qualquer produto do catálogo
$score = $api->consulta->generic('cpf', ['cpf' => '00000000000', 'tipo' => 'serasa-score-pf']);

// homologação (sandbox, sem cobrança)
$teste = $api->consulta->cpf(['cpf' => '00000000000', 'homolog' => true]);
```

### Veículos e FIPE (device-based)

```php
$dados = $api->vehicles->dados(['placa' => 'ABC1234']);
$fipe = $api->vehicles->fipe(['placa' => 'ABC1234']);
```

### SMS

```php
$api->sms->send(['number' => '5511999999999', 'message' => 'Seu código: 123456']);
// ou debitando créditos da conta (sem device):
$api->sms->sendWithCredits(['number' => '5511999999999', 'message' => 'Olá!']);
```

### Pagamentos e recargas

```php
$pix = $api->payments->pixGenerate('inter', ['amount' => 100]);
$status = $api->payments->pixStatus('inter', $pix['txId']);

$boleto = $api->payments->boletoGenerate('sicoob', ['amount' => 150]);
$pdf = $api->payments->boletoPdf('sicoob', $boleto['id']); // conteúdo binário
```

### Múltiplos devices

```php
$comercial = $api->withDevice('DEVICE_TOKEN_COMERCIAL');
$suporte = $api->withDevice('DEVICE_TOKEN_SUPORTE');

$comercial->whatsapp->sendText(['number' => '55...', 'text' => 'Proposta enviada!']);
$suporte->whatsapp->sendText(['number' => '55...', 'text' => 'Como posso ajudar?']);
```

## Tratamento de erros

Cada categoria de falha tem a sua própria classe — todas estendem `ApiBrasilError`
(que por sua vez estende `RuntimeException`):

| Classe                          | Quando                                     |
| ------------------------------- | ------------------------------------------ |
| `ValidationError`               | 400/422 — payload inválido                 |
| `AuthenticationError`           | 401 — token ausente/expirado               |
| `InsufficientBalanceError`      | 402 — sem saldo/créditos                   |
| `PermissionError`               | 403 — sem permissão (ex: exige PJ)         |
| `NotFoundError`                 | 404/410 — sem dados / rota desativada      |
| `RateLimitError`                | 429 — limite atingido (`getRetryAfterMs`)  |
| `ServerError`                   | 5xx — erro do gateway/provedor             |
| `NetworkError` / `TimeoutError` | falha antes da resposta                    |

```php
use ApiBrasil\Core\Errors\InsufficientBalanceError;
use ApiBrasil\Core\Errors\RateLimitError;

try {
    $api->consulta->cpf(['cpf' => '00000000000']);
} catch (InsufficientBalanceError $e) {
    echo 'Recarregue seus créditos';
} catch (RateLimitError $e) {
    echo "Aguarde {$e->getRetryAfterMs()}ms";
}
```

Todo erro expõe `getStatus()` (HTTP), `getErrorCode()` (código da API) e
`getResponse()` (corpo completo da resposta).

## Retry e observabilidade

Por padrão a SDK refaz a chamada em **HTTP 429** e em **falhas de conexão** (2 tentativas extras, backoff exponencial, respeitando `Retry-After`). Timeouts e erros de negócio nunca são refeitos — evita duplicar cobranças e envios.

```php
$api = new ApiBrasil([
    'retry' => ['retries' => 3, 'minDelayMs' => 500, 'retryOnStatuses' => [429, 503]], // ou 'retry' => false
    'hooks' => [
        'onRequest' => fn (array $i) => printf("→ %s %s (#%d)\n", $i['method'], $i['url'], $i['attempt']),
        'onResponse' => fn (array $i) => printf("← %d em %dms\n", $i['status'], $i['durationMs']),
        'onRetry' => fn (array $i) => printf("retry em %dms: %s\n", $i['delayMs'], $i['reason']),
    ],
]);
```

## Transporte plugável

O HTTP é feito pelo Guzzle (com fallback para cURL), mas a interface `TransportInterface`
permite trocar a camada inteira (proxy corporativo, outro cliente, mocks de teste):

```php
use ApiBrasil\ApiBrasil;
use ApiBrasil\Core\Transport\GuzzleTransport;

// Guzzle com opções próprias (proxy, verify, handler...)
$api = new ApiBrasil([
    'transport' => new GuzzleTransport(null, [
        'proxy' => 'http://proxy.local:3128',
        'verify' => true,
    ]),
]);
```

Ou implemente a sua:

```php
use ApiBrasil\Core\Transport\TransportInterface;
use ApiBrasil\Core\Transport\TransportRequest;
use ApiBrasil\Core\Transport\TransportResponse;

final class MeuTransporte implements TransportInterface
{
    public function request(TransportRequest $request): TransportResponse
    {
        // use o cliente HTTP que quiser e devolva status, headers e corpo
        return new TransportResponse(200, [], ['ok' => true]);
    }
}
```

## Catálogo gerado

As actions de WhatsApp/Evolution/WhatsMeow e os 210+ `tipo` de consulta estão
disponíveis em constantes geradas do catálogo real da plataforma
(`composer codegen` atualiza):

```php
use ApiBrasil\Generated\Catalog;

Catalog::WHATSAPP_ACTIONS;                 // ['sendText', 'sendFile', ...]
Catalog::serviceActions('whatsmeow');      // actions documentadas do serviço
Catalog::consultaTipo('lista-socios');     // ['service' => 'cnpj', 'fields' => ['cnpj']]
```

## Endpoint sem método dedicado?

Todo o gateway fica acessível pela porta de saída genérica, já com seus headers de autenticação:

```php
$api->request('POST', '/consulta/cpf/credits', ['cpf' => '00000000000']);
$api->request('GET', '/reports/quick-stats');
```

Documentação completa dos endpoints: https://doc.apibrasil.io

## Configuração avançada

```php
$api = new ApiBrasil([
    'bearerToken' => '...',  // ou APIBRASIL_BEARER_TOKEN
    'deviceToken' => '...',  // ou APIBRASIL_DEVICE_TOKEN
    'secretKey' => '...',    // usada em devices->store (ou APIBRASIL_SECRET_KEY)
    'baseURL' => 'https://gateway.apibrasil.io/api/v2', // padrão (ou APIBRASIL_BASE_URL)
    'timeout' => 30000,      // milissegundos
    'headers' => ['X-Custom' => 'valor'], // headers extras
    'retry' => ['retries' => 2],          // ou false
    'hooks' => ['onRetry' => fn (array $i) => error_log($i['reason'])],
    'transport' => null,     // TransportInterface customizado
]);
```

Opções por requisição (último parâmetro de qualquer método): `query`, `headers`,
`bearerToken`, `deviceToken`, `secretKey`, `timeout`, `responseType`.

```php
$api->whatsapp->sendText(
    ['number' => '5511999999999', 'text' => 'Olá!'],
    ['deviceToken' => 'OUTRO_DEVICE', 'timeout' => 60000]
);
```

> **Atenção:** `timeout` é em **milissegundos** (igual à SDK Node), diferente da
> interface legada, que usa segundos.

## Interface legada (`ApiBrasil\Service`)

Os métodos estáticos `Service::WhatsApp()`, `Service::CEP()`, `Service::CNPJ()` e
companhia continuam funcionando exatamente como antes (resposta em `stdClass`,
erros devolvidos no corpo em vez de exceções), mas estão **deprecados** — prefira
o cliente `ApiBrasil`.

<details>
<summary>Exemplos da interface legada</summary>

### AuthService

```php
$login = Service::Auth("login", [
    "body" => [
        "email" => "jhondoe@gmail.com",
        "password" => "123456"
    ]
]);

$logout = Service::Auth("logout", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
]);
```

### DeviceService

```php
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

$show = Service::Device("show", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "method" => "GET",
    "query" => ["search" => "82faab0a-24f4-4b8b-9926-455ea9b3cdb7"],
]);
```

### ServerService / ProfileService / PlanService

```php
$servers = Service::Server(["Bearer" => "...", "method" => "GET"]);
$profile = Service::Profile(["Bearer" => "...", "method" => "GET"]);
$plans   = Service::Plan("all", ["Bearer" => "...", "method" => "GET"]);
$plan    = Service::Plan("me",  ["Bearer" => "...", "method" => "GET"]);
```

### WhatsAppService

```php
$sendText = Service::WhatsApp("sendText", [
    "Bearer" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.....",
    "DeviceToken" => "d019580b-3c8c-40e3-b9a0....",
    "body" => [
        "number" => "5531994359434",
        "text" => "🟢 Bem vindo ao APIBrasil"
    ]
]);
```

### VehiclesService

```php
$dados = Service::Vehicles("dados", [
    "Bearer" => "...",
    "DeviceToken" => "...",
    "body" => ["placa" => "HBM6603"]
]);

$fipe = Service::Vehicles("fipe", [
    "Bearer" => "...",
    "DeviceToken" => "...",
    "body" => ["placa" => "HBM6603"]
]);

// Vehicles Base (tipo como chave principal)
$base = Service::Vehicles("base/000/dados", [
    "Bearer" => "...",
    "timeout" => 120,
    "body" => [
        "tipo" => "fipe",
        "placa" => "HBM6603",
        "homolog" => false,
    ]
]);

// equivalente — a SDK normaliza o tipo automaticamente
$base = Service::Vehicles("base/000/dados", [
    "Bearer" => "...",
    "timeout" => 120,
    "body" => [
        "fipe" => ["placa" => "HBM6603", "homolog" => false],
    ]
]);
```

### CorreiosService / CNPJService / CEPService / DDD / Feriados

```php
$rastreio = Service::Correios("rastreio", [
    "Bearer" => "...", "DeviceToken" => "...",
    "body" => ["code" => "NL249695552BR"]
]);

$cnpj = Service::CNPJ("cnpj", [
    "Bearer" => "...", "DeviceToken" => "...",
    "body" => ["cnpj" => "44.959.669/0001-80"]
]);

$cep = Service::CEP("cep", [
    "Bearer" => "...", "DeviceToken" => "...",
    "body" => ["cep" => "32146057"]
]);

$ddd = Service::DDD("ddd", [
    "Bearer" => "...", "DeviceToken" => "...",
    "body" => ["ddd" => "31"]
]);

$feriados = Service::HoliDays("feriados", [
    "Bearer" => "...", "DeviceToken" => "...",
    "body" => ["type" => "facultativo", "date" => "07/09", "year" => "2024"]
]);
```

### Opções de request (legado)

- `timeout` (**segundos**): equivalente ao `--max-time` do curl
- `connect_timeout` (segundos): timeout de conexão
- `verify` (bool): valida SSL (padrão atual: `false`)
- `query` (array): querystring (GET/POST)

</details>

## Mais informações

https://packagist.org/packages/jhowbhz/apigratis-sdk-php
