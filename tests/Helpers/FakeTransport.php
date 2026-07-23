<?php

declare(strict_types=1);

namespace ApiBrasil\Test\Helpers;

use ApiBrasil\Core\Transport\TransportInterface;
use ApiBrasil\Core\Transport\TransportRequest;
use ApiBrasil\Core\Transport\TransportResponse;
use Throwable;

/**
 * Transporte fake para testes: grava todas as requisições e responde
 * com uma fila programável (ou um fallback 200).
 */
final class FakeTransport implements TransportInterface
{
    /** @var TransportRequest[] */
    public array $calls = [];

    /** @var array<int,TransportResponse|Throwable> */
    private array $queue = [];

    private TransportResponse $fallback;

    public function __construct()
    {
        $this->fallback = self::ok();
    }

    /** Enfileira respostas (ou erros a lançar), consumidas em ordem. */
    public function respondWith(...$responses): self
    {
        foreach ($responses as $response) {
            $this->queue[] = $response;
        }

        return $this;
    }

    /** Define a resposta padrão quando a fila está vazia. */
    public function setFallback(TransportResponse $response): self
    {
        $this->fallback = $response;

        return $this;
    }

    public function last(): TransportRequest
    {
        if ($this->calls === []) {
            throw new \RuntimeException('Nenhuma requisição foi feita.');
        }

        return $this->calls[count($this->calls) - 1];
    }

    /**
     * Body JSON decodificado da última requisição.
     *
     * @return array<string,mixed>|null
     */
    public function lastBody(): ?array
    {
        $body = $this->last()->body;
        if ($body === null) {
            return null;
        }

        /** @var array<string,mixed>|null $decoded */
        $decoded = json_decode($body, true);

        return $decoded;
    }

    /** @return array<string,string> */
    public function lastHeaders(): array
    {
        return $this->last()->headers;
    }

    public function count(): int
    {
        return count($this->calls);
    }

    public function request(TransportRequest $request): TransportResponse
    {
        $this->calls[] = $request;
        $next = array_shift($this->queue) ?? $this->fallback;

        if ($next instanceof Throwable) {
            throw $next;
        }

        return $next;
    }

    /**
     * @param mixed $data
     */
    public static function ok($data = ['ok' => true]): TransportResponse
    {
        return new TransportResponse(200, [], $data);
    }

    /**
     * @param mixed                $data
     * @param array<string,string> $headers
     */
    public static function httpError(int $status, $data, array $headers = []): TransportResponse
    {
        return new TransportResponse($status, $headers, $data);
    }
}
