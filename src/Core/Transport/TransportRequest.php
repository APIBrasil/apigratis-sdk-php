<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Transport;

/**
 * Requisição entregue à camada de transporte — já com URL absoluta,
 * headers montados e corpo serializado.
 */
final class TransportRequest
{
    /**
     * @param array<string,string> $headers
     * @param string|null          $body         corpo já serializado (JSON)
     * @param int|null             $timeoutMs    timeout em milissegundos
     * @param string|null          $responseType `json` (padrão), `raw`/`arraybuffer` ou `stream`
     */
    public function __construct(
        public string $method,
        public string $url,
        public array $headers = [],
        public ?string $body = null,
        public ?int $timeoutMs = null,
        public ?string $responseType = null
    ) {
    }
}
