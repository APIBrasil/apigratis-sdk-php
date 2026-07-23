<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Errors;

use Throwable;

/**
 * Mapeia um status HTTP + corpo de erro para a subclasse adequada de
 * `ApiBrasilError` (equivalente ao `createApiError` da SDK JS).
 */
final class ErrorFactory
{
    /**
     * @param mixed                     $data
     * @param array<string,string>|null $headers
     */
    public static function create(
        int $status,
        $data,
        ?array $headers = null,
        ?Throwable $previous = null
    ): ApiBrasilError {
        $message = self::extractMessage($status, $data);
        $options = [
            'status' => $status,
            'code' => self::extractCode($data),
            'response' => $data,
            'previous' => $previous,
        ];

        if ($status === 400 || $status === 422) {
            return new ValidationError($message, $options);
        }

        if ($status === 401) {
            return new AuthenticationError($message, $options);
        }

        if ($status === 402) {
            return new InsufficientBalanceError($message, $options);
        }

        if ($status === 403) {
            return new PermissionError($message, $options);
        }

        if ($status === 404 || $status === 410) {
            return new NotFoundError($message, $options);
        }

        if ($status === 429) {
            $options['retryAfterMs'] = self::parseRetryAfterHeader($headers);

            return new RateLimitError($message, $options);
        }

        if ($status >= 500) {
            return new ServerError($message, $options);
        }

        return new ApiBrasilError($message, $options);
    }

    /**
     * @param mixed $data
     */
    private static function extractMessage(int $status, $data): string
    {
        if (is_array($data)) {
            if (isset($data['message']) && is_string($data['message']) && $data['message'] !== '') {
                return $data['message'];
            }

            if (isset($data['error']) && is_string($data['error']) && $data['error'] !== '') {
                return $data['error'];
            }
        }

        return "A API respondeu com HTTP {$status}.";
    }

    /**
     * @param mixed $data
     */
    private static function extractCode($data): ?string
    {
        if (is_array($data) && isset($data['code']) && is_string($data['code'])) {
            return $data['code'];
        }

        return null;
    }

    /**
     * Lê o header `Retry-After` (segundos ou data HTTP) e devolve ms.
     *
     * @param array<string,string>|null $headers
     */
    private static function parseRetryAfterHeader(?array $headers): ?int
    {
        if ($headers === null) {
            return null;
        }

        $raw = $headers['retry-after'] ?? $headers['Retry-After'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_numeric($raw)) {
            return (int) max(0, (float) $raw * 1000);
        }

        $at = strtotime((string) $raw);
        if ($at === false) {
            return null;
        }

        return (int) max(0, ($at - time()) * 1000);
    }
}
