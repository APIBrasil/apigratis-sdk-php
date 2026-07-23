<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/** CEP + geolocalização (device-based): `/cep/{action}`. */
class CepService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'cep');
    }

    /**
     * Consulta um CEP: `POST /cep/cep` body `['cep' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cep(array $body, array $options = [])
    {
        return $this->request('cep', $body, $options);
    }
}
