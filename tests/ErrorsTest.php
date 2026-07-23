<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\Core\Errors\ApiBrasilError;
use ApiBrasil\Core\Errors\AuthenticationError;
use ApiBrasil\Core\Errors\ErrorFactory;
use ApiBrasil\Core\Errors\InsufficientBalanceError;
use ApiBrasil\Core\Errors\NotFoundError;
use ApiBrasil\Core\Errors\PermissionError;
use ApiBrasil\Core\Errors\RateLimitError;
use ApiBrasil\Core\Errors\ServerError;
use ApiBrasil\Core\Errors\ValidationError;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ErrorsTest extends TestCase
{
    /**
     * @dataProvider statusMap
     */
    public function test_mapeia_status_para_a_subclasse(int $status, string $expected): void
    {
        $data = ['message' => 'mensagem da api', 'code' => 'X'];
        $error = ErrorFactory::create($status, $data);

        $this->assertInstanceOf($expected, $error);
        $this->assertSame('mensagem da api', $error->getMessage());
        $this->assertSame($status, $error->getStatus());
        $this->assertSame('X', $error->getErrorCode());
        $this->assertSame($data, $error->getResponse());
    }

    /** @return array<string,array{0:int,1:class-string}> */
    public function statusMap(): array
    {
        return [
            'HTTP 400 → ValidationError' => [400, ValidationError::class],
            'HTTP 422 → ValidationError' => [422, ValidationError::class],
            'HTTP 401 → AuthenticationError' => [401, AuthenticationError::class],
            'HTTP 402 → InsufficientBalanceError' => [402, InsufficientBalanceError::class],
            'HTTP 403 → PermissionError' => [403, PermissionError::class],
            'HTTP 404 → NotFoundError' => [404, NotFoundError::class],
            'HTTP 410 → NotFoundError' => [410, NotFoundError::class],
            'HTTP 429 → RateLimitError' => [429, RateLimitError::class],
            'HTTP 500 → ServerError' => [500, ServerError::class],
            'HTTP 503 → ServerError' => [503, ServerError::class],
            'HTTP 418 → ApiBrasilError' => [418, ApiBrasilError::class],
        ];
    }

    public function test_extrai_retry_after_ms_do_header_em_segundos(): void
    {
        $error = ErrorFactory::create(429, [], ['retry-after' => '3']);

        $this->assertInstanceOf(RateLimitError::class, $error);
        $this->assertSame(3000, $error->getRetryAfterMs());
    }

    public function test_usa_mensagem_padrao_quando_o_corpo_nao_tem_message(): void
    {
        $error = ErrorFactory::create(500, 'Internal Server Error');

        $this->assertSame('A API respondeu com HTTP 500.', $error->getMessage());
    }

    public function test_usa_o_campo_error_quando_nao_ha_message(): void
    {
        $error = ErrorFactory::create(400, ['error' => 'Requisição inválida']);

        $this->assertSame('Requisição inválida', $error->getMessage());
    }

    public function test_getters_de_conveniencia_funcionam(): void
    {
        $this->assertTrue(ErrorFactory::create(402, [])->isInsufficientBalance());
        $this->assertTrue(ErrorFactory::create(401, [])->isUnauthorized());
        $this->assertFalse(ErrorFactory::create(500, [])->isUnauthorized());
    }

    public function test_from_mantem_instancias_de_api_brasil_error(): void
    {
        $original = new ValidationError('inválido', ['status' => 422]);

        $this->assertSame($original, ApiBrasilError::from($original));
    }

    public function test_from_converte_erros_do_guzzle_usando_o_status(): void
    {
        $exception = new RequestException(
            'Request failed',
            new Request('POST', '/consulta/cpf/credits'),
            new Response(402, [], (string) json_encode(['message' => 'Sem saldo']))
        );

        $error = ApiBrasilError::from($exception);

        $this->assertInstanceOf(InsufficientBalanceError::class, $error);
        $this->assertSame('Sem saldo', $error->getMessage());
    }

    public function test_from_embrulha_erros_desconhecidos(): void
    {
        $error = ApiBrasilError::from(new \RuntimeException('boom'));

        $this->assertInstanceOf(ApiBrasilError::class, $error);
        $this->assertSame('boom', $error->getMessage());
    }
}
