<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;

/** Chip virtual: `/chip/virtual/*`. */
class ChipVirtualService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista operadoras: `POST /chip/virtual/operators`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function operators(?array $body = null, array $options = [])
    {
        return $this->http->post('/chip/virtual/operators', $body, $options);
    }

    /**
     * Compra um número: `POST /chip/virtual/buy`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function buy(array $body, array $options = [])
    {
        return $this->http->post('/chip/virtual/buy', $body, $options);
    }

    /**
     * Consulta ativação: `POST /chip/virtual/activation`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function activation(array $body, array $options = [])
    {
        return $this->http->post('/chip/virtual/activation', $body, $options);
    }

    /**
     * Lista serviços: `POST /chip/virtual/services`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function services(?array $body = null, array $options = [])
    {
        return $this->http->post('/chip/virtual/services', $body, $options);
    }
}
