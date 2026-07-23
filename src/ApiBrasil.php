<?php

declare(strict_types=1);

namespace ApiBrasil;

use ApiBrasil\Core\Errors\ApiBrasilError;
use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\Data\BulkService;
use ApiBrasil\Services\Data\CepService;
use ApiBrasil\Services\Data\ChipVirtualService;
use ApiBrasil\Services\Data\ConsultaService;
use ApiBrasil\Services\Data\CorreiosService;
use ApiBrasil\Services\Data\DadosService;
use ApiBrasil\Services\Data\DatabaseIpService;
use ApiBrasil\Services\Data\FipeService;
use ApiBrasil\Services\Data\UraService;
use ApiBrasil\Services\Data\VehiclesService;
use ApiBrasil\Services\DeviceProxyService;
use ApiBrasil\Services\Messaging\EvolutionService;
use ApiBrasil\Services\Messaging\SmsService;
use ApiBrasil\Services\Messaging\WhatsAppService;
use ApiBrasil\Services\Messaging\WhatsMeowService;
use ApiBrasil\Services\Platform\AccountService;
use ApiBrasil\Services\Platform\AuthService;
use ApiBrasil\Services\Platform\BearerRateLimitService;
use ApiBrasil\Services\Platform\CatalogService;
use ApiBrasil\Services\Platform\DevicesService;
use ApiBrasil\Services\Platform\IpWhitelistService;
use ApiBrasil\Services\Platform\PaymentsService;
use ApiBrasil\Services\Platform\ReportsService;

/**
 * Cliente oficial da plataforma APIBrasil.
 *
 * ```php
 * use ApiBrasil\ApiBrasil;
 *
 * $api = new ApiBrasil([
 *     'bearerToken' => getenv('APIBRASIL_BEARER_TOKEN'),
 *     'deviceToken' => getenv('APIBRASIL_DEVICE_TOKEN'),
 * ]);
 *
 * $api->whatsapp->sendText(['number' => '5511999999999', 'text' => 'Olá!']);
 * $cnpj = $api->consulta->cnpj(['cnpj' => '00000000000000']);
 * ```
 *
 * Credenciais não informadas são lidas das variáveis de ambiente
 * `APIBRASIL_BEARER_TOKEN`, `APIBRASIL_DEVICE_TOKEN`,
 * `APIBRASIL_SECRET_KEY` e `APIBRASIL_BASE_URL`.
 */
class ApiBrasil
{
    /** Cliente HTTP interno (headers, base URL, retry, hooks, erros). */
    public HttpClient $http;

    /** Login, 2FA, cadastro, senha e perfil. */
    public AuthService $auth;

    /** Gestão de devices (criar, listar, atualizar, remover). */
    public DevicesService $devices;

    /** WhatsApp device-based (`/whatsapp/{action}`). */
    public WhatsAppService $whatsapp;

    /** Evolution API (`/evolution/{controller}/{action}`). */
    public EvolutionService $evolution;

    /** WhatsMeow (`/whatsmeow/{action}`). */
    public WhatsMeowService $whatsmeow;

    /** SMS (`/sms/{action}` e `/sms/send/credits`). */
    public SmsService $sms;

    /** Dados cadastrais device-based (`/dados/cpf`, `/dados/cnpj`...). */
    public DadosService $dados;

    /** Veículos por placa (`/vehicles/dados`, `/vehicles/fipe`). */
    public VehiclesService $vehicles;

    /** Tabela FIPE (`/fipe/{action}`). */
    public FipeService $fipe;

    /** Correios (`/correios/{action}`). */
    public CorreiosService $correios;

    /** CEP + geolocalização (`/cep/{action}`). */
    public CepService $cep;

    /** Geolocalização (`/geolocation/{action}`). */
    public DeviceProxyService $geolocation;

    /** Matriz de distâncias (`/geomatrix/{action}`). */
    public DeviceProxyService $geomatrix;

    /** OCR / Google Vision (`/recognize/{action}`). */
    public DeviceProxyService $recognize;

    /** DDD (`/ddd/{action}`). */
    public DeviceProxyService $ddd;

    /** Feriados (`/holidays/{action}`). */
    public DeviceProxyService $holidays;

    /** Tradução (`/translate/{action}`). */
    public DeviceProxyService $translate;

    /** Clima (`/weather/{action}`). */
    public DeviceProxyService $weather;

    /** Loterias (`/loterias/{action}`). */
    public DeviceProxyService $loterias;

    /** GeoIP (`/database/ip`). */
    public DatabaseIpService $databaseIp;

    /** Consultas por crédito (`/consulta/{servico}/credits`). */
    public ConsultaService $consulta;

    /** URA reversa / ligações (`/ura/call/*`). */
    public UraService $ura;

    /** Chip virtual (`/chip/virtual/*`). */
    public ChipVirtualService $chipVirtual;

    /** Execução em lote (`/bulk/*`). */
    public BulkService $bulk;

    /** Catálogo de APIs, planos, docs e servidores. */
    public CatalogService $catalog;

    /** Saldo, faturas, notificações, tickets. */
    public AccountService $account;

    /** Recargas e pagamentos (PIX, boleto, cartão). */
    public PaymentsService $payments;

    /** IP whitelist da conta. */
    public IpWhitelistService $ipWhitelist;

    /** Rate limit por Bearer Token. */
    public BearerRateLimitService $bearerRateLimit;

    /** Relatórios e dashboard de consumo. */
    public ReportsService $reports;

    /**
     * @param array<string,mixed> $config veja `HttpClient` para as chaves aceitas
     */
    public function __construct(array $config = [])
    {
        $this->http = new HttpClient($config);

        $this->auth = new AuthService($this->http);
        $this->devices = new DevicesService($this->http);

        $this->whatsapp = new WhatsAppService($this->http);
        $this->evolution = new EvolutionService($this->http);
        $this->whatsmeow = new WhatsMeowService($this->http);
        $this->sms = new SmsService($this->http);

        $this->dados = new DadosService($this->http);
        $this->vehicles = new VehiclesService($this->http);
        $this->fipe = new FipeService($this->http);
        $this->correios = new CorreiosService($this->http);
        $this->cep = new CepService($this->http);
        $this->geolocation = new DeviceProxyService($this->http, 'geolocation');
        $this->geomatrix = new DeviceProxyService($this->http, 'geomatrix');
        $this->recognize = new DeviceProxyService($this->http, 'recognize');
        $this->ddd = new DeviceProxyService($this->http, 'ddd');
        $this->holidays = new DeviceProxyService($this->http, 'holidays');
        $this->translate = new DeviceProxyService($this->http, 'translate');
        $this->weather = new DeviceProxyService($this->http, 'weather');
        $this->loterias = new DeviceProxyService($this->http, 'loterias');
        $this->databaseIp = new DatabaseIpService($this->http);

        $this->consulta = new ConsultaService($this->http);
        $this->ura = new UraService($this->http);
        $this->chipVirtual = new ChipVirtualService($this->http);
        $this->bulk = new BulkService($this->http);

        $this->catalog = new CatalogService($this->http);
        $this->account = new AccountService($this->http);
        $this->payments = new PaymentsService($this->http);
        $this->ipWhitelist = new IpWhitelistService($this->http);
        $this->bearerRateLimit = new BearerRateLimitService($this->http);
        $this->reports = new ReportsService($this->http);
    }

    /**
     * Faz login e retorna um cliente já autenticado.
     * Lança `ApiBrasilError` se a conta exigir 2FA — nesse caso crie o
     * cliente manualmente e use `auth->login()` + `auth->verify2fa()`.
     *
     * @param array<string,mixed> $credentials `email` e `password`
     * @param array<string,mixed> $config
     *
     * @return array{client:self,session:mixed}
     */
    public static function login(array $credentials, array $config = []): array
    {
        $client = new self($config);
        $session = $client->auth->login($credentials);

        if (is_array($session) && !empty($session['requires_2fa'])) {
            throw new ApiBrasilError(
                'Esta conta exige autenticação em dois fatores. Use auth->login() + auth->verify2fa().',
                ['response' => $session]
            );
        }

        return ['client' => $client, 'session' => $session];
    }

    /** Define/atualiza o Bearer Token do cliente. */
    public function setBearerToken(string $token): self
    {
        $this->http->setBearerToken($token);

        return $this;
    }

    /** Define/atualiza o DeviceToken do cliente. */
    public function setDeviceToken(string $token): self
    {
        $this->http->setDeviceToken($token);

        return $this;
    }

    /**
     * Retorna um novo cliente com as mesmas credenciais, mas apontando
     * para outro device — útil para gerenciar vários números/instâncias.
     */
    public function withDevice(string $deviceToken): self
    {
        $config = $this->http->getConfig();
        $config['deviceToken'] = $deviceToken;

        return new self($config);
    }

    /**
     * Porta de saída genérica: chama qualquer endpoint do gateway com os
     * headers de autenticação já configurados. Use para rotas que ainda
     * não têm método dedicado na SDK.
     *
     * ```php
     * $api->request('POST', '/consulta/cpf/credits', ['cpf' => '...']);
     * ```
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function request(string $method, string $path, ?array $body = null, array $options = [])
    {
        return $this->http->request($method, $path, $body, $options);
    }
}
