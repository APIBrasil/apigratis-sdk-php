<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Errors;

/** HTTP 429 — rate limit atingido. */
class RateLimitError extends ApiBrasilError
{
    /** Espera sugerida pelo servidor (header `Retry-After`), em ms. */
    protected ?int $retryAfterMs;

    /**
     * @param array{status?:int|null,code?:string|null,response?:mixed,previous?:\Throwable|null,retryAfterMs?:int|null} $options
     */
    public function __construct(string $message, array $options = [])
    {
        parent::__construct($message, $options);
        $this->retryAfterMs = $options['retryAfterMs'] ?? null;
    }

    /** Espera sugerida pelo servidor (header `Retry-After`), em ms. */
    public function getRetryAfterMs(): ?int
    {
        return $this->retryAfterMs;
    }
}
