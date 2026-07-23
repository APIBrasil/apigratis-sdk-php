<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * IP Whitelist da conta (`/ip-whitelist/*`).
 * Restringe de quais IPs o Bearer Token pode ser usado.
 */
class IpWhitelistService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Configuração atual: `GET /ip-whitelist`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function get(array $options = [])
    {
        return $this->http->get('/ip-whitelist', $options);
    }

    /**
     * Substitui a lista inteira: `PUT /ip-whitelist`.
     *
     * @param string|string[]     $ipWhitelist
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function set($ipWhitelist, array $options = [])
    {
        return $this->http->put('/ip-whitelist', ['ip_whitelist' => $ipWhitelist], $options);
    }

    /**
     * Adiciona uma entrada: `POST /ip-whitelist/add`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function add(string $entry, array $options = [])
    {
        return $this->http->post('/ip-whitelist/add', ['entry' => $entry], $options);
    }

    /**
     * Remove uma entrada: `DELETE /ip-whitelist/remove`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function remove(string $entry, array $options = [])
    {
        return $this->http->delete('/ip-whitelist/remove', ['entry' => $entry], $options);
    }

    /**
     * Adiciona o IP atual: `POST /ip-whitelist/add-current`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function addCurrent(array $options = [])
    {
        return $this->http->post('/ip-whitelist/add-current', null, $options);
    }

    /**
     * Libera todos os IPs (wildcard): `POST /ip-whitelist/reset`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function reset(array $options = [])
    {
        return $this->http->post('/ip-whitelist/reset', null, $options);
    }

    /**
     * Valida uma entrada: `POST /ip-whitelist/validate`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function validate(string $entry, array $options = [])
    {
        return $this->http->post('/ip-whitelist/validate', ['entry' => $entry], $options);
    }

    /**
     * IP atual visto pelo gateway: `GET /ip-whitelist/current-ip`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function currentIp(array $options = [])
    {
        return $this->http->get('/ip-whitelist/current-ip', $options);
    }
}
