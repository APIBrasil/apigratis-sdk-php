<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Messaging;

use ApiBrasil\Core\HttpClient;

/**
 * WhatsMeow API (device-based): `POST /whatsmeow/{action}`.
 * Exige `Authorization: Bearer` + `DeviceToken`.
 */
class WhatsMeowService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Executa `POST /whatsmeow/{action}`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function request(string $action, ?array $body = null, array $options = [])
    {
        return $this->http->post('/whatsmeow/'.ltrim($action, '/'), $body, $options);
    }

    /**
     * Executa a mesma chamada de forma assíncrona via fila.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function queue(string $action, ?array $body = null, array $options = [])
    {
        return $this->http->post('/whatsmeow/'.ltrim($action, '/').'/queue', $body, $options);
    }
}
