<?php

declare(strict_types=1);

namespace ApiBrasil\Core;

/**
 * Leitura da configuração a partir das variáveis de ambiente.
 */
final class Env
{
    /** Variáveis de ambiente reconhecidas pela SDK. */
    public const VARS = [
        'bearerToken' => 'APIBRASIL_BEARER_TOKEN',
        'deviceToken' => 'APIBRASIL_DEVICE_TOKEN',
        'secretKey' => 'APIBRASIL_SECRET_KEY',
        'baseURL' => 'APIBRASIL_BASE_URL',
    ];

    /**
     * Lê a configuração das variáveis de ambiente (quando disponíveis).
     * Valores passados explicitamente no construtor sempre têm prioridade.
     *
     * @return array<string,string>
     */
    public static function config(): array
    {
        $config = [];

        foreach (self::VARS as $key => $name) {
            $value = self::read($name);
            if ($value !== null && $value !== '') {
                $config[$key] = $value;
            }
        }

        return $config;
    }

    private static function read(string $name): ?string
    {
        if (array_key_exists($name, $_ENV) && is_scalar($_ENV[$name])) {
            return (string) $_ENV[$name];
        }

        if (array_key_exists($name, $_SERVER) && is_scalar($_SERVER[$name])) {
            return (string) $_SERVER[$name];
        }

        $value = getenv($name);

        return $value === false ? null : $value;
    }
}
