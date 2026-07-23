<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\Core\Errors\NetworkError;
use ApiBrasil\Core\Errors\TimeoutError;
use ApiBrasil\Core\Transport\GuzzleTransport;
use ApiBrasil\Core\Transport\TransportRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class TransportTest extends TestCase
{
    /** @var array<int,array<string,mixed>> */
    private array $history = [];

    /**
     * @param array<int,mixed> $queue
     */
    private function transport(array $queue): GuzzleTransport
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler($queue));
        $stack->push(Middleware::history($this->history));

        return new GuzzleTransport(new Client(['handler' => $stack]));
    }

    private function baseRequest(?string $responseType = null): TransportRequest
    {
        return new TransportRequest(
            'POST',
            'https://example.com/api/v2/x',
            ['Content-Type' => 'application/json'],
            '{"a":1}',
            null,
            $responseType
        );
    }

    public function test_faz_o_parse_de_respostas_json_e_repassa_status_e_headers(): void
    {
        $transport = $this->transport([
            new Response(201, ['Content-Type' => 'application/json', 'X-Custom' => 'v'], (string) json_encode(['ok' => true])),
        ]);

        $response = $transport->request($this->baseRequest());

        $this->assertSame(201, $response->status);
        $this->assertSame(['ok' => true], $response->data);
        $this->assertSame('v', $response->headers['x-custom']);

        /** @var RequestInterface $sent */
        $sent = $this->history[0]['request'];
        $this->assertSame('POST', $sent->getMethod());
        $this->assertSame('{"a":1}', (string) $sent->getBody());
    }

    public function test_retorna_texto_puro_quando_o_corpo_nao_e_json(): void
    {
        $transport = $this->transport([new Response(200, [], '<html>oops</html>')]);

        $this->assertSame('<html>oops</html>', $transport->request($this->baseRequest())->data);
    }

    public function test_retorna_null_para_corpo_vazio(): void
    {
        $transport = $this->transport([new Response(204, [], '')]);

        $response = $transport->request($this->baseRequest());

        $this->assertSame(204, $response->status);
        $this->assertNull($response->data);
    }

    public function test_response_type_raw_devolve_o_conteudo_bruto(): void
    {
        $transport = $this->transport([new Response(200, [], (string) json_encode(['ok' => true]))]);

        $this->assertSame('{"ok":true}', $transport->request($this->baseRequest('raw'))->data);
    }

    public function test_response_type_stream_devolve_o_body_da_resposta(): void
    {
        $transport = $this->transport([new Response(200, [], 'fluxo')]);

        $data = $transport->request($this->baseRequest('stream'))->data;

        $this->assertInstanceOf(\Psr\Http\Message\StreamInterface::class, $data);
        $this->assertSame('fluxo', (string) $data);
    }

    public function test_nao_lanca_excecao_em_status_de_erro(): void
    {
        $transport = $this->transport([new Response(422, [], (string) json_encode(['message' => 'inválido']))]);

        $response = $transport->request($this->baseRequest());

        $this->assertSame(422, $response->status);
        $this->assertSame(['message' => 'inválido'], $response->data);
    }

    public function test_lanca_network_error_quando_a_conexao_falha(): void
    {
        $transport = $this->transport([
            new ConnectException('Connection refused', new Request('POST', 'https://example.com')),
        ]);

        $this->expectException(NetworkError::class);
        $transport->request($this->baseRequest());
    }

    public function test_lanca_timeout_error_quando_a_requisicao_estoura_o_tempo(): void
    {
        $transport = $this->transport([
            new ConnectException(
                'cURL error 28: Operation timed out',
                new Request('POST', 'https://example.com'),
                null,
                ['errno' => 28]
            ),
        ]);

        $this->expectException(TimeoutError::class);
        $transport->request($this->baseRequest());
    }
}
