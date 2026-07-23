# Changelog

## 0.1.5 — 2026-07-23

Novo cliente `ApiBrasil\ApiBrasil` cobrindo toda a plataforma APIBrasil — mesma
arquitetura, mesmos endpoints e mesmas funções da SDK Node.js. Release
totalmente retrocompatível: nada da interface antiga foi alterado.

### Novidades

- **Cliente central `ApiBrasil\ApiBrasil`** com módulos por produto: `whatsapp`, `evolution`, `whatsmeow`, `sms`, `dados`, `vehicles`, `fipe`, `correios`, `cep`, `geolocation`, `geomatrix`, `recognize`, `ddd`, `holidays`, `translate`, `weather`, `loterias`, `databaseIp`, `consulta` (créditos), `ura`, `chipVirtual`, `bulk`, `auth` (login/2FA), `devices`, `catalog`, `account`, `payments` (PIX/boleto/cartão), `ipWhitelist`, `bearerRateLimit`, `reports`.
- **Transporte plugável** (`TransportInterface`): `GuzzleTransport` por padrão, `CurlTransport` sem dependências como fallback, e injeção de implementações próprias para proxies e mocks.
- **Retry com backoff exponencial** (padrão: HTTP 429 e falhas de conexão; nunca timeouts nem erros de negócio) com suporte a `Retry-After`.
- **Hooks de observabilidade**: `onRequest`, `onResponse`, `onRetry`.
- **Hierarquia de erros**: `ValidationError`, `AuthenticationError`, `InsufficientBalanceError`, `PermissionError`, `NotFoundError`, `RateLimitError`, `ServerError`, `NetworkError`, `TimeoutError` — todas estendendo `ApiBrasilError`.
- **Variáveis de ambiente**: `APIBRASIL_BEARER_TOKEN`, `APIBRASIL_DEVICE_TOKEN`, `APIBRASIL_SECRET_KEY`, `APIBRASIL_BASE_URL` lidas automaticamente.
- **Catálogo gerado** (`composer codegen`): `Catalog::WHATSAPP_ACTIONS`, `EVOLUTION_PATHS`, `WHATSMEOW_ACTIONS`, `CONSULTA_SERVICOS`, `CONSULTA_TIPOS` (210+ tipos) e `SERVICE_ACTIONS`.
- **Testes** unitários com transporte fake (257 casos, cobrindo todas as rotas e todo o catálogo) e de contrato opcionais (`composer test:contract`).

### Compatibilidade

- A interface legada `ApiBrasil\Service` (`Service::WhatsApp()`, `Service::CEP()`, ...) continua funcionando com o **mesmo contrato**: sempre `POST` por padrão, resposta em `stdClass` e erros HTTP devolvidos no corpo em vez de exceções. Está marcada como deprecated — prefira `new ApiBrasil([...])`.
- `Base::defaultRequest()` e `Service::*` aceitam agora `handler` nas opções (handler stack do Guzzle), útil para proxies e testes.

### Notas

- No cliente novo, `timeout` é em **milissegundos** (paridade com a SDK Node). A interface legada continua em segundos.
- As respostas do cliente novo são **arrays associativos**; a interface legada continua devolvendo `stdClass`.

## 0.1.4

Interface estática `ApiBrasil\Service` sobre Guzzle: WhatsApp, veículos,
correios, CEP, CNPJ, DDD, feriados, devices, planos e perfil.
