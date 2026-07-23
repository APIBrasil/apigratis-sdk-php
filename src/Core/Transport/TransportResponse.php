<?php

declare(strict_types=1);

namespace ApiBrasil\Core\Transport;

/**
 * Resposta devolvida pela camada de transporte.
 */
final class TransportResponse
{
    /**
     * @param array<string,string> $headers headers com nomes em minúsculas
     * @param mixed                $data    corpo já decodificado (JSON → array; texto; binário; stream)
     */
    public function __construct(
        public int $status,
        public array $headers = [],
        public $data = null
    ) {
    }
}
