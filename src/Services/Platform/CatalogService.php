<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Catálogo público da plataforma: APIs, planos, documentações e servidores.
 * A maioria das rotas é pública (não exige Bearer Token).
 */
class CatalogService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista/busca APIs disponíveis: `GET /apis?search=`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function apis(?string $search = null, array $options = [])
    {
        $options['query'] = array_merge(
            $search !== null && $search !== '' ? ['search' => $search] : [],
            $options['query'] ?? []
        );

        return $this->http->get('/apis', $options);
    }

    /**
     * Detalha uma API por identificador: `GET /apis/{identifier}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function api(string $identifier, array $options = [])
    {
        return $this->http->get('/apis/'.$identifier, $options);
    }

    /**
     * Detalha uma API pelo nome: `GET /apis/name/{name}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function apiByName(string $name, array $options = [])
    {
        return $this->http->get('/apis/name/'.rawurlencode($name), $options);
    }

    /**
     * Categorias de APIs: `GET /apis/categories`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function apiCategories(array $options = [])
    {
        return $this->http->get('/apis/categories', $options);
    }

    /**
     * APIs contratadas pelo usuário (autenticado): `GET /apis/list`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function myApis(array $options = [])
    {
        return $this->http->get('/apis/list', $options);
    }

    /**
     * APIs vinculadas a um device: `GET /apis/device/{device_token}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function apisByDevice(string $deviceToken, array $options = [])
    {
        return $this->http->get('/apis/device/'.$deviceToken, $options);
    }

    /**
     * Planos disponíveis: `GET /plans`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function plans(array $options = [])
    {
        return $this->http->get('/plans', $options);
    }

    /**
     * Documentações: `GET /documentations`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function documentations(array $options = [])
    {
        return $this->http->get('/documentations', $options);
    }

    /**
     * Documentação por servidor: `GET /documentations/server/{server_search}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function documentationsByServer(string $serverSearch, array $options = [])
    {
        return $this->http->get('/documentations/server/'.$serverSearch, $options);
    }

    /**
     * Servidores disponíveis: `GET /servers`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function servers(array $options = [])
    {
        return $this->http->get('/servers', $options);
    }

    /**
     * Resolve a URL de uma action (descoberta dinâmica de endpoints):
     * `POST /endpoint/url`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function endpointUrl(array $body, array $options = [])
    {
        return $this->http->post('/endpoint/url', $body, $options);
    }

    /**
     * Body esperado por uma action: `POST /endpoint/body`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function endpointBody(array $body, array $options = [])
    {
        return $this->http->post('/endpoint/body', $body, $options);
    }

    /**
     * Status do gateway: `GET /status`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function status(array $options = [])
    {
        return $this->http->get('/status', $options);
    }
}
