<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Gestão de devices (`/devices/*`).
 *
 * Devices são a credencial de consumo dos serviços device-based:
 * crie um device com a `SecretKey` da API desejada e use o
 * `device_token` retornado como header `DeviceToken`.
 */
class DevicesService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista os devices do usuário: `GET /devices`.
     *
     * @param array<string,mixed> $query   ex: `['paginate' => true]`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function list(array $query = [], array $options = [])
    {
        $options['query'] = array_merge($query, $options['query'] ?? []);

        return $this->http->get('/devices', $options);
    }

    /**
     * Cria um device: `POST /devices/store`.
     *
     * A `SecretKey` da API (painel APIBrasil) vai no header — passe em
     * `$options['secretKey']` ou configure `secretKey` no cliente.
     *
     * @param array<string,mixed> $body    `device_name`, `type`, `device_key`, webhooks...
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function store(array $body, array $options = [])
    {
        $secretKey = $options['secretKey'] ?? $this->http->getSecretKey();
        if ($secretKey !== null) {
            $options['secretKey'] = $secretKey;
        }

        return $this->http->post('/devices/store', $body, $options);
    }

    /**
     * Detalha um device: `GET /devices/show?search={device_token}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function show(?string $deviceToken = null, array $options = [])
    {
        $search = $deviceToken ?? $this->http->getDeviceToken();
        $options['query'] = array_merge(['search' => $search], $options['query'] ?? []);

        return $this->http->get('/devices/show', $options);
    }

    /**
     * Atualiza um device: `POST /devices/update` (body com `device_token` + campos).
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function update(array $body, array $options = [])
    {
        return $this->http->post('/devices/update', $body, $options);
    }

    /**
     * Remove um device: `DELETE /devices/destroy`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function destroy(?string $deviceToken = null, array $options = [])
    {
        $search = $deviceToken ?? $this->http->getDeviceToken();

        return $this->http->delete('/devices/destroy', ['search' => $search], $options);
    }

    /**
     * Histórico de requisições do device: `POST /devices/requests`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function requests(?array $body = null, array $options = [])
    {
        return $this->http->post('/devices/requests', $body, $options);
    }
}
