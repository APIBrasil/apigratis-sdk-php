<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Messaging;

use ApiBrasil\Core\HttpClient;

/**
 * Evolution API (device-based): `POST /evolution/{controller}/{action}`.
 * Exige `Authorization: Bearer` + `DeviceToken`.
 *
 * Exemplos de controller/action: `instance/create`, `message/sendText`.
 * Os caminhos documentados estão em `ApiBrasil\Generated\Catalog::EVOLUTION_PATHS`.
 */
class EvolutionService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Executa `POST /evolution/{controller}/{action}`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function request(string $controller, string $action, ?array $body = null, array $options = [])
    {
        return $this->http->post("/evolution/{$controller}/{$action}", $body, $options);
    }

    /**
     * Executa um caminho completo do catálogo: `POST /evolution/{path}`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function call(string $path, ?array $body = null, array $options = [])
    {
        return $this->http->post('/evolution/'.ltrim($path, '/'), $body, $options);
    }

    /**
     * Executa a mesma chamada de forma assíncrona via fila.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function queue(string $controller, string $action, ?array $body = null, array $options = [])
    {
        return $this->http->post("/evolution/{$controller}/{$action}/queue", $body, $options);
    }
}
