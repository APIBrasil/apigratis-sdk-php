<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;

/**
 * Consultas por crédito (`/consulta/{servico}/credits` e afins).
 *
 * Não usam `DeviceToken` — debitam o saldo/créditos da conta.
 * O campo `tipo` do body define o produto consultado (ex: `lista-socios`,
 * `serasa-score-pf`, `spc-serasa`) — a lista completa está em
 * `ApiBrasil\Generated\Catalog::CONSULTA_TIPOS`. Use `homolog => true`
 * para sandbox.
 */
class ConsultaService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Consulta genérica: `POST /consulta/{service}/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function generic(string $service, array $body = [], array $options = [])
    {
        return $this->http->post("/consulta/{$service}/credits", $body, $options);
    }

    /**
     * Consulta CPF: `POST /consulta/cpf/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cpf(array $body, array $options = [])
    {
        return $this->generic('cpf', $body, $options);
    }

    /**
     * Consulta CNPJ: `POST /consulta/cnpj/credits` (ex: `tipo => 'lista-socios'`).
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cnpj(array $body, array $options = [])
    {
        return $this->generic('cnpj', $body, $options);
    }

    /**
     * Consulta CNH: `POST /consulta/cnh/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cnh(array $body, array $options = [])
    {
        return $this->generic('cnh', $body, $options);
    }

    /**
     * Consulta CEP: `POST /consulta/cep/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cep(array $body, array $options = [])
    {
        return $this->generic('cep', $body, $options);
    }

    /**
     * Consulta veicular: `POST /consulta/veiculos/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function veiculos(array $body, array $options = [])
    {
        return $this->generic('veiculos', $body, $options);
    }

    /**
     * Consulta operadora de telefone: `POST /consulta/telefone/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function telefone(array $body, array $options = [])
    {
        return $this->generic('telefone', $body, $options);
    }

    /**
     * Consulta veicular nas bases dedicadas:
     * `POST /vehicles/base/000/dados` | `POST /vehicles/base/000/fipe`.
     *
     * @param string              $base `dados` ou `fipe`
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function veiculosBase(string $base, array $body, array $options = [])
    {
        return $this->http->post("/vehicles/base/000/{$base}", $body, $options);
    }

    /**
     * Distância entre CEPs: `POST /cep/distancia/calcular`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cepDistancia(array $body, array $options = [])
    {
        return $this->http->post('/cep/distancia/calcular', $body, $options);
    }

    /**
     * Proxy seller: `POST /proxy/seller/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function proxySeller(array $body = [], array $options = [])
    {
        return $this->http->post('/proxy/seller/credits', $body, $options);
    }
}
