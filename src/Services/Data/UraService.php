<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;

/** URA reversa / ligações: `/ura/call/*`. */
class UraService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Disca uma ligação: `POST /ura/call/dialler`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function dialler(array $body, array $options = [])
    {
        return $this->http->post('/ura/call/dialler', $body, $options);
    }

    /**
     * Consulta status da ligação: `POST /ura/call/status`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function status(array $body, array $options = [])
    {
        return $this->http->post('/ura/call/status', $body, $options);
    }
}
