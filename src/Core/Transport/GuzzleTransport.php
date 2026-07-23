<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Transport;

use ApiBrasil\Core\Errors\NetworkError;
use ApiBrasil\Core\Errors\TimeoutError;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Transporte padrão baseado no Guzzle (`guzzlehttp/guzzle`).
 */
final class GuzzleTransport implements TransportInterface
{
    private ClientInterface $client;

    /** @var array<string,mixed> */
    private array $options;

    /**
     * @param array<string,mixed> $options opções extras repassadas ao Guzzle
     *                                     (ex: `proxy`, `verify`, `handler`)
     */
    public function __construct(?ClientInterface $client = null, array $options = [])
    {
        $this->client = $client ?? new Client();
        $this->options = $options;
    }

    public function request(TransportRequest $request): TransportResponse
    {
        $options = array_merge([
            'headers' => $request->headers,
            'http_errors' => false,
            'allow_redirects' => true,
        ], $this->options);

        if ($request->body !== null) {
            $options['body'] = $request->body;
        }

        if ($request->timeoutMs !== null && $request->timeoutMs > 0) {
            $options['timeout'] = $request->timeoutMs / 1000;
            $options['connect_timeout'] ??= $request->timeoutMs / 1000;
        }

        if ($request->responseType === 'stream') {
            $options['stream'] = true;
        }

        try {
            /** @var ResponseInterface $response */
            $response = $this->client->request($request->method, $request->url, $options);
        } catch (ConnectException $error) {
            throw self::connectionError($request, $error);
        } catch (GuzzleException $error) {
            throw new NetworkError(
                sprintf(
                    'Falha de rede em %s %s: %s',
                    $request->method,
                    $request->url,
                    $error->getMessage()
                ),
                ['previous' => $error]
            );
        }

        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[strtolower((string) $name)] = implode(', ', $values);
        }

        return new TransportResponse(
            $response->getStatusCode(),
            $headers,
            self::decodeBody($request, $response)
        );
    }

    /**
     * @return mixed
     */
    private static function decodeBody(TransportRequest $request, ResponseInterface $response)
    {
        if ($request->responseType === 'stream') {
            return $response->getBody();
        }

        $raw = (string) $response->getBody();

        if ($request->responseType === 'raw' || $request->responseType === 'arraybuffer') {
            return $raw;
        }

        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $raw;
    }

    private static function connectionError(
        TransportRequest $request,
        ConnectException $error
    ): NetworkError {
        $errno = $error->getHandlerContext()['errno'] ?? null;
        // 28 = CURLE_OPERATION_TIMEDOUT
        $timedOut = $errno === 28
            || stripos($error->getMessage(), 'timed out') !== false
            || stripos($error->getMessage(), 'timeout') !== false;

        if ($timedOut) {
            return new TimeoutError(
                sprintf(
                    'Tempo limite de %sms excedido em %s %s.',
                    $request->timeoutMs ?? 0,
                    $request->method,
                    $request->url
                ),
                ['previous' => $error]
            );
        }

        return new NetworkError(
            sprintf(
                'Falha de rede em %s %s: %s',
                $request->method,
                $request->url,
                $error->getMessage()
            ),
            ['previous' => $error]
        );
    }
}
