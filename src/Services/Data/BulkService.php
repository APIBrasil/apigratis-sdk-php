<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;

/** Execução em lote: `/bulk/direct/{action}` e `/bulk/queue/{action}`. */
class BulkService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Executa um lote de forma síncrona: `POST /bulk/direct/{action}`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function direct(string $action, array $body, array $options = [])
    {
        return $this->http->post('/bulk/direct/'.ltrim($action, '/'), $body, $options);
    }

    /**
     * Enfileira um lote: `POST /bulk/queue/{action}`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function queue(string $action, array $body, array $options = [])
    {
        return $this->http->post('/bulk/queue/'.ltrim($action, '/'), $body, $options);
    }
}
