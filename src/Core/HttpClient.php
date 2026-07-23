<?php

declare(strict_types=1);

namespace ApiBrasil\Core;

use ApiBrasil\Core\Errors\ApiBrasilError;
use ApiBrasil\Core\Errors\ErrorFactory;
use ApiBrasil\Core\Errors\NetworkError;
use ApiBrasil\Core\Errors\RateLimitError;
use ApiBrasil\Core\Errors\TimeoutError;
use ApiBrasil\Core\Transport\CurlTransport;
use ApiBrasil\Core\Transport\GuzzleTransport;
use ApiBrasil\Core\Transport\TransportInterface;
use ApiBrasil\Core\Transport\TransportRequest;
use Throwable;

/**
 * Cliente HTTP interno da SDK. Injeta os headers de autenticação da
 * plataforma (`Authorization: Bearer`, `DeviceToken`, `SecretKey`),
 * aplica retry com backoff, dispara hooks de observabilidade e converte
 * falhas em subclasses de `ApiBrasilError`.
 *
 * Chaves de configuração aceitas:
 * - `bearerToken` (string): token JWT obtido no login
 * - `deviceToken` (string): token do dispositivo (serviços device-based)
 * - `secretKey`   (string): SecretKey da API (usada ao criar devices)
 * - `baseURL`     (string): base da API. Padrão: `https://gateway.apibrasil.io/api/v2`
 * - `timeout`     (int): timeout das requisições em **milissegundos**. Padrão: 30000
 * - `headers`     (array): headers adicionais em todas as requisições
 * - `transport`   (TransportInterface): transporte customizado
 * - `retry`       (array|false): política de retry — veja `Retry::DEFAULT`
 * - `hooks`       (array): `onRequest`, `onResponse`, `onRetry` (callables)
 *
 * Opções por requisição (`$options`), que sobrescrevem a configuração:
 * `query`, `headers`, `bearerToken`, `deviceToken`, `secretKey`, `timeout`,
 * `responseType` (`json` | `raw` | `stream`).
 */
class HttpClient
{
    public const DEFAULT_BASE_URL = 'https://gateway.apibrasil.io/api/v2';
    public const DEFAULT_TIMEOUT = 30000;
    public const SDK_USER_AGENT = 'APIBRASIL/SDK-PHP';

    /** @var array<string,mixed> */
    private array $config;

    private TransportInterface $transport;

    /** @var array{retries:int,minDelayMs:int,maxDelayMs:int,retryOnStatuses:int[]}|null */
    private ?array $retry;

    /** @var array<string,callable> */
    private array $hooks;

    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge(
            Env::config(),
            array_filter($config, static fn ($value) => $value !== null)
        );

        $transport = $this->config['transport'] ?? null;
        $this->transport = $transport instanceof TransportInterface
            ? $transport
            : self::defaultTransport();

        $this->retry = Retry::resolve($this->config['retry'] ?? null);

        /** @var array<string,callable> $hooks */
        $hooks = is_array($this->config['hooks'] ?? null) ? $this->config['hooks'] : [];
        $this->hooks = $hooks;
    }

    public function getBaseUrl(): string
    {
        $baseURL = $this->config['baseURL'] ?? null;

        return is_string($baseURL) && $baseURL !== '' ? $baseURL : self::DEFAULT_BASE_URL;
    }

    public function getBearerToken(): ?string
    {
        $token = $this->config['bearerToken'] ?? null;

        return is_string($token) ? $token : null;
    }

    public function getDeviceToken(): ?string
    {
        $token = $this->config['deviceToken'] ?? null;

        return is_string($token) ? $token : null;
    }

    public function getSecretKey(): ?string
    {
        $key = $this->config['secretKey'] ?? null;

        return is_string($key) ? $key : null;
    }

    public function setBearerToken(?string $token): void
    {
        if ($token === null) {
            unset($this->config['bearerToken']);

            return;
        }

        $this->config['bearerToken'] = $token;
    }

    public function setDeviceToken(?string $token): void
    {
        if ($token === null) {
            unset($this->config['deviceToken']);

            return;
        }

        $this->config['deviceToken'] = $token;
    }

    /**
     * Configuração atual do cliente (útil para clonar com outro device).
     *
     * @return array<string,mixed>
     */
    public function getConfig(): array
    {
        $config = $this->config;
        $config['transport'] = $this->transport;

        return $config;
    }

    public function getTransport(): TransportInterface
    {
        return $this->transport;
    }

    /**
     * Executa uma requisição no gateway.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed corpo da resposta já decodificado
     */
    public function request(string $method, string $path, ?array $body = null, array $options = [])
    {
        $method = strtoupper($method);
        $headers = $this->buildHeaders($options);
        $url = self::joinUrl($this->getBaseUrl(), $path).self::buildQueryString($options['query'] ?? null);
        $serializedBody = $body === null ? null : self::encodeBody($body);
        $timeoutMs = (int) ($options['timeout'] ?? $this->config['timeout'] ?? self::DEFAULT_TIMEOUT);
        $responseType = $options['responseType'] ?? null;
        $maxAttempts = 1 + ($this->retry['retries'] ?? 0);

        $attempt = 0;
        while (true) {
            $this->fireHook('onRequest', [
                'method' => $method,
                'url' => $url,
                'headers' => $headers,
                'body' => $body,
                'attempt' => $attempt,
            ]);

            $startedAt = microtime(true);

            try {
                $response = $this->transport->request(new TransportRequest(
                    $method,
                    $url,
                    $headers,
                    $serializedBody,
                    $timeoutMs,
                    is_string($responseType) ? $responseType : null
                ));
            } catch (Throwable $error) {
                $retryable = $error instanceof NetworkError && !$error instanceof TimeoutError;

                if ($retryable && $this->retry !== null && $attempt + 1 < $maxAttempts) {
                    $delayMs = Retry::backoffDelayMs($attempt, $this->retry);
                    ++$attempt;
                    $this->fireHook('onRetry', [
                        'method' => $method,
                        'url' => $url,
                        'attempt' => $attempt,
                        'delayMs' => $delayMs,
                        'reason' => $error->getMessage(),
                    ]);
                    Retry::sleep($delayMs);

                    continue;
                }

                throw ApiBrasilError::from($error);
            }

            $this->fireHook('onResponse', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status,
                'durationMs' => (int) round((microtime(true) - $startedAt) * 1000),
                'attempt' => $attempt,
            ]);

            if ($response->status >= 400) {
                $error = ErrorFactory::create($response->status, $response->data, $response->headers);
                $retryableStatus = in_array(
                    $response->status,
                    $this->retry['retryOnStatuses'] ?? [],
                    true
                );

                if ($retryableStatus && $this->retry !== null && $attempt + 1 < $maxAttempts) {
                    $delayMs = $error instanceof RateLimitError && $error->getRetryAfterMs() !== null
                        ? $error->getRetryAfterMs()
                        : Retry::backoffDelayMs($attempt, $this->retry);
                    ++$attempt;
                    $this->fireHook('onRetry', [
                        'method' => $method,
                        'url' => $url,
                        'attempt' => $attempt,
                        'delayMs' => $delayMs,
                        'reason' => 'HTTP '.$response->status,
                    ]);
                    Retry::sleep((int) $delayMs);

                    continue;
                }

                throw $error;
            }

            return $response->data;
        }
    }

    /**
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function get(string $path, array $options = [])
    {
        return $this->request('GET', $path, null, $options);
    }

    /**
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function post(string $path, ?array $body = null, array $options = [])
    {
        return $this->request('POST', $path, $body, $options);
    }

    /**
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function put(string $path, ?array $body = null, array $options = [])
    {
        return $this->request('PUT', $path, $body, $options);
    }

    /**
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function patch(string $path, ?array $body = null, array $options = [])
    {
        return $this->request('PATCH', $path, $body, $options);
    }

    /**
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function delete(string $path, ?array $body = null, array $options = [])
    {
        return $this->request('DELETE', $path, $body, $options);
    }

    /**
     * @param array<string,mixed> $options
     *
     * @return array<string,string>
     */
    private function buildHeaders(array $options): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => self::SDK_USER_AGENT,
        ];

        if (is_array($this->config['headers'] ?? null)) {
            /** @var array<string,string> $configured */
            $configured = $this->config['headers'];
            $headers = array_merge($headers, $configured);
        }

        $bearerToken = $options['bearerToken'] ?? $this->getBearerToken();
        if (is_string($bearerToken) && $bearerToken !== '') {
            $headers['Authorization'] = 'Bearer '.$bearerToken;
        }

        $deviceToken = $options['deviceToken'] ?? $this->getDeviceToken();
        if (is_string($deviceToken) && $deviceToken !== '') {
            $headers['DeviceToken'] = $deviceToken;
        }

        $secretKey = $options['secretKey'] ?? null;
        if (is_string($secretKey) && $secretKey !== '') {
            $headers['SecretKey'] = $secretKey;
        }

        if (is_array($options['headers'] ?? null)) {
            /** @var array<string,string> $extra */
            $extra = $options['headers'];
            $headers = array_merge($headers, $extra);
        }

        return $headers;
    }

    /**
     * @param array<string,mixed> $info
     */
    private function fireHook(string $name, array $info): void
    {
        $hook = $this->hooks[$name] ?? null;
        if (is_callable($hook)) {
            $hook($info);
        }
    }

    /**
     * Serializa o corpo em JSON. Arrays vazios viram `{}` (objeto), como
     * na SDK JS, e nunca `[]`.
     *
     * @param array<string,mixed> $body
     */
    private static function encodeBody(array $body): string
    {
        if ($body === []) {
            return '{}';
        }

        return (string) json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array<string,mixed>|null $query
     */
    private static function buildQueryString(?array $query): string
    {
        if ($query === null || $query === []) {
            return '';
        }

        $parts = [];
        foreach ($query as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_array($value)) {
                $value = implode(',', array_map(static fn ($item) => (string) $item, $value));
            }

            $parts[] = urlencode((string) $key).'='.urlencode((string) $value);
        }

        return $parts === [] ? '' : '?'.implode('&', $parts);
    }

    private static function joinUrl(string $baseURL, string $path): string
    {
        $base = rtrim($baseURL, '/');
        $suffix = ltrim($path, '/');

        return $suffix === '' ? $base : $base.'/'.$suffix;
    }

    private static function defaultTransport(): TransportInterface
    {
        if (class_exists(\GuzzleHttp\Client::class)) {
            return new GuzzleTransport();
        }

        return new CurlTransport();
    }
}
