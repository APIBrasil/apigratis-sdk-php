<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Transport;

use ApiBrasil\Core\Errors\NetworkError;
use ApiBrasil\Core\Errors\TimeoutError;

/**
 * Transporte sem dependências, usando apenas a extensão cURL do PHP.
 * Usado automaticamente quando o Guzzle não está instalado.
 */
final class CurlTransport implements TransportInterface
{
    /** @var array<int,mixed> */
    private array $curlOptions;

    /**
     * @param array<int,mixed> $curlOptions opções extras (ex: `CURLOPT_PROXY`)
     */
    public function __construct(array $curlOptions = [])
    {
        $this->curlOptions = $curlOptions;
    }

    public function request(TransportRequest $request): TransportResponse
    {
        $handle = curl_init();
        if ($handle === false) {
            throw new NetworkError('Não foi possível inicializar o cURL.');
        }

        $headers = [];
        foreach ($request->headers as $name => $value) {
            $headers[] = $name.': '.$value;
        }

        $responseHeaders = [];

        $options = [
            CURLOPT_URL => $request->url,
            CURLOPT_CUSTOMREQUEST => $request->method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADERFUNCTION => static function ($_handle, $line) use (&$responseHeaders) {
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }

                return strlen($line);
            },
        ];

        if ($request->body !== null) {
            $options[CURLOPT_POSTFIELDS] = $request->body;
        }

        if ($request->timeoutMs !== null && $request->timeoutMs > 0) {
            $options[CURLOPT_TIMEOUT_MS] = $request->timeoutMs;
            $options[CURLOPT_CONNECTTIMEOUT_MS] = $request->timeoutMs;
        }

        curl_setopt_array($handle, $options + $this->curlOptions);

        $raw = curl_exec($handle);
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        if ($raw === false || $errno !== 0) {
            if ($errno === CURLE_OPERATION_TIMEOUTED) {
                throw new TimeoutError(sprintf(
                    'Tempo limite de %sms excedido em %s %s.',
                    $request->timeoutMs ?? 0,
                    $request->method,
                    $request->url
                ));
            }

            throw new NetworkError(sprintf(
                'Falha de rede em %s %s: %s',
                $request->method,
                $request->url,
                $error !== '' ? $error : 'erro desconhecido'
            ));
        }

        return new TransportResponse(
            $status,
            $responseHeaders,
            self::decodeBody($request, (string) $raw)
        );
    }

    /**
     * @return mixed
     */
    private static function decodeBody(TransportRequest $request, string $raw)
    {
        if (
            $request->responseType === 'raw'
            || $request->responseType === 'arraybuffer'
            || $request->responseType === 'stream'
        ) {
            return $raw;
        }

        if ($raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $raw;
    }
}
