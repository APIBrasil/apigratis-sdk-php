<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Rate limit por Bearer Token (`/bearer-rate-limit`).
 */
class BearerRateLimitService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Limite atual: `GET /bearer-rate-limit`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function get(array $options = [])
    {
        return $this->http->get('/bearer-rate-limit', $options);
    }

    /**
     * Define o limite por minuto: `PUT /bearer-rate-limit`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function set(array $body, array $options = [])
    {
        return $this->http->put('/bearer-rate-limit', $body, $options);
    }
}
