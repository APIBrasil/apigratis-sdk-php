<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\Core\Errors\NetworkError;
use ApiBrasil\Core\Errors\RateLimitError;
use ApiBrasil\Core\Errors\TimeoutError;
use ApiBrasil\Core\Errors\ValidationError;
use ApiBrasil\Core\HttpClient;
use ApiBrasil\Test\Helpers\FakeTransport;
use PHPUnit\Framework\TestCase;

class RetryTest extends TestCase
{
    /** @var array{retries:int,minDelayMs:int,maxDelayMs:int} */
    private const FAST_RETRY = ['retries' => 2, 'minDelayMs' => 1, 'maxDelayMs' => 5];

    public function test_tenta_novamente_em_429_e_retorna_a_resposta_seguinte(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::httpError(429, ['message' => 'Rate limit']),
            FakeTransport::ok(['done' => true])
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => self::FAST_RETRY]);

        $result = $http->post('/whatsapp/sendText', []);

        $this->assertSame(['done' => true], $result);
        $this->assertCount(2, $transport->calls);
    }

    public function test_respeita_o_header_retry_after(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::httpError(429, ['message' => 'Rate limit'], ['retry-after' => '0']),
            FakeTransport::ok()
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => self::FAST_RETRY]);

        $http->post('/x', []);

        $this->assertCount(2, $transport->calls);
    }

    public function test_esgota_as_tentativas_e_lanca_rate_limit_error(): void
    {
        $transport = (new FakeTransport())->setFallback(
            FakeTransport::httpError(429, ['message' => 'Rate limit'])
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => self::FAST_RETRY]);

        try {
            $http->post('/x', []);
            $this->fail('Deveria ter lançado RateLimitError.');
        } catch (RateLimitError $error) {
            $this->assertCount(3, $transport->calls); // original + 2 retries
        }
    }

    public function test_nao_tenta_novamente_em_erros_de_negocio(): void
    {
        $transport = (new FakeTransport())->setFallback(
            FakeTransport::httpError(422, ['message' => 'Inválido'])
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => self::FAST_RETRY]);

        try {
            $http->post('/x', []);
            $this->fail('Deveria ter lançado ValidationError.');
        } catch (ValidationError $error) {
            $this->assertCount(1, $transport->calls);
        }
    }

    public function test_tenta_novamente_em_falhas_de_rede(): void
    {
        $transport = (new FakeTransport())->respondWith(
            new NetworkError('conexão recusada'),
            FakeTransport::ok()
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => self::FAST_RETRY]);

        $this->assertSame(['ok' => true], $http->post('/x', []));
        $this->assertCount(2, $transport->calls);
    }

    public function test_nao_tenta_novamente_em_timeout(): void
    {
        $transport = (new FakeTransport())->respondWith(
            new TimeoutError('estourou'),
            FakeTransport::ok()
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => self::FAST_RETRY]);

        try {
            $http->post('/x', []);
            $this->fail('Deveria ter lançado TimeoutError.');
        } catch (TimeoutError $error) {
            $this->assertCount(1, $transport->calls);
        }
    }

    public function test_retry_false_desativa_completamente(): void
    {
        $transport = (new FakeTransport())->setFallback(
            FakeTransport::httpError(429, ['message' => 'Rate limit'])
        );
        $http = new HttpClient(['transport' => $transport, 'retry' => false]);

        try {
            $http->post('/x', []);
            $this->fail('Deveria ter lançado RateLimitError.');
        } catch (RateLimitError $error) {
            $this->assertCount(1, $transport->calls);
        }
    }

    public function test_dispara_hooks_on_request_on_response_e_on_retry(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::httpError(429, ['message' => 'Rate limit']),
            FakeTransport::ok()
        );

        $requests = [];
        $responses = [];
        $retries = [];

        $http = new HttpClient([
            'transport' => $transport,
            'retry' => self::FAST_RETRY,
            'hooks' => [
                'onRequest' => static function (array $info) use (&$requests): void {
                    $requests[] = $info;
                },
                'onResponse' => static function (array $info) use (&$responses): void {
                    $responses[] = $info;
                },
                'onRetry' => static function (array $info) use (&$retries): void {
                    $retries[] = $info;
                },
            ],
        ]);

        $http->post('/whatsapp/sendText', ['number' => '55']);

        $this->assertCount(2, $requests);
        $this->assertSame('POST', $requests[0]['method']);
        $this->assertSame(0, $requests[0]['attempt']);

        $this->assertCount(2, $responses);
        $this->assertSame(429, $responses[0]['status']);
        $this->assertSame(0, $responses[0]['attempt']);
        $this->assertSame(200, $responses[1]['status']);
        $this->assertSame(1, $responses[1]['attempt']);

        $this->assertCount(1, $retries);
        $this->assertSame(1, $retries[0]['attempt']);
        $this->assertSame('HTTP 429', $retries[0]['reason']);
    }
}
