<?php

declare(strict_types=1);

namespace ApiBrasil\Services;

use ApiBrasil\Core\HttpClient;

/**
 * Base dos serviços "device-based" do gateway (`/api/v2/{servico}/{action}`).
 * Exigem `Authorization: Bearer` + header `DeviceToken`.
 *
 * Todos expõem `request($action, $body)` como porta de saída genérica —
 * as actions são dinâmicas por provedor, consulte a documentação em
 * https://doc.apibrasil.io
 *
 * A resposta segue o envelope device-based
 * (`['error' => ..., 'message' => ..., 'response' => ...]`).
 */
class DeviceProxyService
{
    protected HttpClient $http;

    /** Nome do serviço no gateway (primeiro segmento da rota). */
    protected string $service;

    public function __construct(HttpClient $http, string $service)
    {
        $this->http = $http;
        $this->service = $service;
    }

    /** Nome do serviço no gateway. */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Executa uma action do serviço: `POST /{servico}/{action}`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function request(string $action, ?array $body = null, array $options = [])
    {
        return $this->http->post(
            '/'.$this->service.'/'.trim($action, '/'),
            $body,
            $options
        );
    }
}
