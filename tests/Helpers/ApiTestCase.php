<?php

declare(strict_types=1);

namespace ApiBrasil\Test\Helpers;

use ApiBrasil\ApiBrasil;
use PHPUnit\Framework\TestCase;

/**
 * Base dos testes de rota: cliente `ApiBrasil` com transporte fake,
 * retry desligado e credenciais fixas.
 */
abstract class ApiTestCase extends TestCase
{
    public const BASE = 'https://gateway.apibrasil.io/api/v2';

    protected FakeTransport $transport;

    protected ApiBrasil $api;

    protected function setUp(): void
    {
        parent::setUp();
        [$this->transport, $this->api] = $this->buildApi();
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array{0:FakeTransport,1:ApiBrasil}
     */
    protected function buildApi(array $config = []): array
    {
        $transport = new FakeTransport();
        $api = new ApiBrasil(array_merge([
            'transport' => $transport,
            'retry' => false,
            'baseURL' => self::BASE,
            'bearerToken' => 'jwt',
            'deviceToken' => 'dev',
        ], $config));

        return [$transport, $api];
    }

    /**
     * Executa um caso de rota: uma chamada da SDK e a requisição esperada.
     *
     * @param callable(ApiBrasil):mixed $call
     * @param array<string,mixed>|null  $body
     */
    protected function assertRoute(
        callable $call,
        string $path,
        string $method = 'POST',
        ?array $body = null
    ): void {
        [$transport, $api] = $this->buildApi();
        $call($api);

        $this->assertSame($method, $transport->last()->method);
        $this->assertSame(self::BASE.$path, $transport->last()->url);

        if ($body !== null) {
            $this->assertEquals($body, $transport->lastBody());
        }
    }
}
