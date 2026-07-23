<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Transport;

/**
 * Camada de transporte HTTP da SDK. A implementação padrão usa Guzzle
 * (`GuzzleTransport`), com fallback para cURL (`CurlTransport`); injete a
 * sua para usar proxies, outro cliente HTTP, mocks de teste etc.
 *
 * Contrato: retorna a resposta para QUALQUER status HTTP; lança
 * `NetworkError`/`TimeoutError` apenas quando não houve resposta.
 */
interface TransportInterface
{
    public function request(TransportRequest $request): TransportResponse;
}
