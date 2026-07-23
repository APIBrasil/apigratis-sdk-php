<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/** Veículos por placa (device-based): `/vehicles/{action}`. */
class VehiclesService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'vehicles');
    }

    /**
     * Dados do veículo pela placa: `POST /vehicles/dados` body `['placa' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function dados(array $body, array $options = [])
    {
        return $this->request('dados', $body, $options);
    }

    /**
     * FIPE pela placa: `POST /vehicles/fipe` body `['placa' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function fipe(array $body, array $options = [])
    {
        return $this->request('fipe', $body, $options);
    }

    /**
     * Consulta FIPE: `POST /vehicles/consultafipe/{placa}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function consultaFipe(string $placa, array $options = [])
    {
        return $this->request('consultafipe/'.rawurlencode($placa), null, $options);
    }
}
