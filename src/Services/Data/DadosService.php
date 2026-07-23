<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/** Dados cadastrais (device-based): `/dados/{action}`. */
class DadosService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'dados');
    }

    /**
     * Consulta CNPJ: `POST /dados/cnpj` body `['cnpj' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cnpj(array $body, array $options = [])
    {
        return $this->request('cnpj', $body, $options);
    }

    /**
     * Consulta CPF: `POST /dados/cpf` body `['cpf' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cpf(array $body, array $options = [])
    {
        return $this->request('cpf', $body, $options);
    }
}
