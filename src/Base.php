<?php

namespace ApiBrasil;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Transporte da interface legada (v1.x/v2.x da SDK).
 *
 * @deprecated Prefira o cliente `ApiBrasil\ApiBrasil`, que cobre toda a
 *             plataforma com métodos dedicados, hierarquia de erros,
 *             retry e hooks. Esta classe é mantida por compatibilidade.
 *
 * @see \ApiBrasil\ApiBrasil
 */
class Base
{
    /**
     * @param array<string,mixed> $headers
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options `timeout` e `connect_timeout` em segundos,
     *                                     `verify` (bool), `query` (array) e
     *                                     `handler` (handler stack do Guzzle)
     *
     * @return mixed
     */
    public static function defaultRequest(
        string $method,
        string $base_uri,
        string $action,
        array $headers = [],
        array $body = [],
        array $options = []
    )
    {
        try {

            $clientConfig = [
                'timeout' => (int)($options['timeout'] ?? 300), // seconds
                'connect_timeout' => (int)($options['connect_timeout'] ?? 30), // seconds
                'verify' => (bool)($options['verify'] ?? false), // keep backwards-compatible default
                'base_uri' => rtrim($base_uri, '/').'/',
                'http_errors' => false,
            ];

            // permite injetar um handler stack (MockHandler nos testes, proxies etc.)
            if (isset($options['handler'])) {
                $clientConfig['handler'] = $options['handler'];
            }

            $client = new Client($clientConfig);

            $headers['Content-Type'] ??= 'application/json';
            $headers['Accept'] ??= 'application/json';

            $headers['User-Agent'] ??= 'ApiBrasil/2.0.0 (https://www.apibrasil.io)';

            $requestOptions = [
                'headers' => $headers,
            ];

            $query = $options['query'] ?? null;
            if (is_array($query) && $query !== []) {
                $requestOptions['query'] = $query;
            }

            $methodUpper = strtoupper($method);
            if (!in_array($methodUpper, ['GET', 'HEAD'], true) && $body !== []) {
                $requestOptions['json'] = $body;
            }

            $response = $client->request($methodUpper, ltrim($action, '/'), $requestOptions);
            $raw = (string) $response->getBody();

            $actionPath = explode('?', ltrim($action, '/'))[0] ?? '';
            if (trim($actionPath, '/') === 'qrcode') {
                return $raw;
            }

            $decoded = json_decode($raw);
            return $decoded ?? $raw;

        } catch (ClientException $e) {

            // return exception error
            $response = $e->getResponse();
            return json_decode((string)($response->getBody()->getContents()));

        } catch (\Throwable $e) {

            return (object)[
                'error' => true,
                'message' => $e->getMessage(),
            ];

        }
    }
}
