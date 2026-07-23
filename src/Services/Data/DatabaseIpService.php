<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;

/** GeoIP (device-based): `POST /database/ip`. */
class DatabaseIpService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Consulta a base de IPs: `POST /database/ip` body `['ip' => ...]`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function ip(?array $body = null, array $options = [])
    {
        return $this->http->post('/database/ip', $body, $options);
    }
}
