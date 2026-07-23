<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Errors;

use RuntimeException;
use Throwable;

/**
 * Erro base lançado pelo cliente `ApiBrasil`. Subclasses específicas
 * permitem tratar cada categoria com `instanceof`:
 *
 * - `ValidationError` (400/422), `AuthenticationError` (401),
 *   `InsufficientBalanceError` (402), `PermissionError` (403),
 *   `NotFoundError` (404/410), `RateLimitError` (429), `ServerError` (5xx)
 * - `NetworkError` / `TimeoutError` para falhas antes da resposta.
 */
class ApiBrasilError extends RuntimeException
{
    /** Status HTTP retornado pela API (ex: 401, 402, 404). */
    protected ?int $status;

    /** Código de erro retornado pela API (ex: `NOT_FOUND`). */
    protected ?string $errorCode;

    /**
     * Corpo completo da resposta de erro, quando existir.
     *
     * @var mixed
     */
    protected $response;

    /**
     * @param array{status?:int|null,code?:string|null,response?:mixed,previous?:Throwable|null} $options
     */
    public function __construct(string $message, array $options = [])
    {
        parent::__construct(
            $message,
            (int) ($options['status'] ?? 0),
            $options['previous'] ?? null
        );

        $this->status = $options['status'] ?? null;
        $this->errorCode = $options['code'] ?? null;
        $this->response = $options['response'] ?? null;
    }

    /** Status HTTP retornado pela API. */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /** Código de erro retornado pela API (equivalente a `error.code` na SDK JS). */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Corpo completo da resposta de erro.
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /** `true` quando a falha foi por saldo/créditos insuficientes (HTTP 402). */
    public function isInsufficientBalance(): bool
    {
        return $this->status === 402;
    }

    /** `true` quando a falha foi de autenticação (HTTP 401). */
    public function isUnauthorized(): bool
    {
        return $this->status === 401;
    }

    /**
     * Converte qualquer erro em um `ApiBrasilError`, preservando status e
     * corpo quando a exceção original carrega uma resposta HTTP (Guzzle).
     *
     * @param mixed $error
     */
    public static function from($error): ApiBrasilError
    {
        if ($error instanceof ApiBrasilError) {
            return $error;
        }

        if ($error instanceof \GuzzleHttp\Exception\RequestException && $error->hasResponse()) {
            $response = $error->getResponse();
            $raw = (string) $response->getBody();
            $data = json_decode($raw, true);

            return ErrorFactory::create(
                $response->getStatusCode(),
                $data === null ? $raw : $data,
                self::normalizeHeaders($response->getHeaders()),
                $error
            );
        }

        if ($error instanceof Throwable) {
            return new ApiBrasilError($error->getMessage(), ['previous' => $error]);
        }

        return new ApiBrasilError(is_scalar($error) ? (string) $error : 'Erro desconhecido.');
    }

    /**
     * @param array<string,array<int,string>> $headers
     *
     * @return array<string,string>
     */
    private static function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $values) {
            $normalized[strtolower((string) $name)] = implode(', ', $values);
        }

        return $normalized;
    }
}
