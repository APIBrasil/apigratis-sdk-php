<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/** Correios (device-based): `/correios/{action}`. */
class CorreiosService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'correios');
    }

    /**
     * Rastreio de encomendas: `POST /correios/rastreio` body `['code' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function rastreio(array $body, array $options = [])
    {
        return $this->request('rastreio', $body, $options);
    }
}
