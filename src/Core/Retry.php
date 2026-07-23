<?php

declare(strict_types=1);

namespace ApiBrasil\Core;

/**
 * Política de retry do cliente. Por padrão a SDK tenta novamente apenas
 * em HTTP 429 (rate limit) e em falhas de conexão — nunca em timeouts ou
 * erros de negócio, para não duplicar cobranças/envios.
 */
final class Retry
{
    /**
     * Configuração padrão:
     * - `retries`: novas tentativas além da original
     * - `minDelayMs`: atraso base do backoff exponencial
     * - `maxDelayMs`: teto do atraso entre tentativas
     * - `retryOnStatuses`: status HTTP que disparam retry
     *
     * @var array{retries:int,minDelayMs:int,maxDelayMs:int,retryOnStatuses:int[]}
     */
    public const DEFAULT = [
        'retries' => 2,
        'minDelayMs' => 300,
        'maxDelayMs' => 5000,
        'retryOnStatuses' => [429],
    ];

    /**
     * Normaliza a configuração de retry. `false` desativa completamente.
     *
     * @param array<string,mixed>|false|null $config
     *
     * @return array{retries:int,minDelayMs:int,maxDelayMs:int,retryOnStatuses:int[]}|null
     */
    public static function resolve($config): ?array
    {
        if ($config === false) {
            return null;
        }

        if (!is_array($config)) {
            return self::DEFAULT;
        }

        /** @var array{retries:int,minDelayMs:int,maxDelayMs:int,retryOnStatuses:int[]} $resolved */
        $resolved = array_merge(self::DEFAULT, array_filter(
            $config,
            static fn ($value) => $value !== null
        ));

        return $resolved;
    }

    /**
     * Backoff exponencial com jitter: min * 2^attempt, limitado a max.
     *
     * @param array{retries:int,minDelayMs:int,maxDelayMs:int,retryOnStatuses:int[]} $retry
     */
    public static function backoffDelayMs(int $attempt, array $retry): int
    {
        $exponential = $retry['minDelayMs'] * (2 ** $attempt);
        $jitter = 0.5 + (mt_rand() / mt_getrandmax()) * 0.5;

        return (int) min($retry['maxDelayMs'], (int) round($exponential * $jitter));
    }

    /** Pausa a execução por `$ms` milissegundos. */
    public static function sleep(int $ms): void
    {
        if ($ms > 0) {
            usleep($ms * 1000);
        }
    }
}
