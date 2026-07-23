<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\Service;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Garante que a interface legada (`ApiBrasil\Service`) continua com o
 * mesmo contrato: sempre POST por padrão, resposta em `stdClass` e erros
 * HTTP devolvidos como corpo — sem lançar exceção.
 */
class LegacyServiceTest extends TestCase
{
    /** @var array<int,array<string,mixed>> */
    private array $history = [];

    /**
     * @param array<int,mixed> $queue
     *
     * @return array<string,mixed> opções para os métodos de `Service`
     */
    private function handler(array $queue): array
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler($queue));
        $stack->push(Middleware::history($this->history));

        return ['handler' => $stack];
    }

    private function lastRequest(): RequestInterface
    {
        /** @var RequestInterface $request */
        $request = $this->history[count($this->history) - 1]['request'];

        return $request;
    }

    public function test_e_uma_instancia_de_service(): void
    {
        $this->assertInstanceOf(Service::class, new Service());
    }

    public function test_whatsapp_envia_post_com_headers_legados(): void
    {
        $options = $this->handler([new Response(200, [], (string) json_encode(['ok' => true]))]);

        $response = Service::WhatsApp('sendText', array_merge($options, [
            'Bearer' => 'jwt',
            'DeviceToken' => 'dev',
            'SecretKey' => 'sk',
            'body' => ['number' => '5511999999999', 'text' => 'Olá'],
        ]));

        $request = $this->lastRequest();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame(
            'https://gateway.apibrasil.io/api/v2/whatsapp/sendText',
            (string) $request->getUri()
        );
        $this->assertSame('Bearer jwt', $request->getHeaderLine('Authorization'));
        $this->assertSame('dev', $request->getHeaderLine('DeviceToken'));
        $this->assertSame('sk', $request->getHeaderLine('SecretKey'));
        $this->assertEquals(
            ['number' => '5511999999999', 'text' => 'Olá'],
            json_decode((string) $request->getBody(), true)
        );
        $this->assertIsObject($response);
        $this->assertTrue($response->ok);
    }

    /**
     * @dataProvider servicosLegados
     *
     * @param array<string,mixed> $extra
     */
    public function test_rotas_legadas(string $method, string $action, string $path, array $extra = []): void
    {
        $options = $this->handler([new Response(200, [], (string) json_encode(['ok' => true]))]);

        $callable = [Service::class, $method];
        $this->assertIsCallable($callable);

        if ($action === '') {
            $callable(array_merge($options, $extra));
        } else {
            $callable($action, array_merge($options, $extra));
        }

        $this->assertSame(
            'https://gateway.apibrasil.io/api/v2'.$path,
            (string) $this->lastRequest()->getUri()
        );
    }

    /** @return array<string,array{0:string,1:string,2:string,3?:array<string,mixed>}> */
    public function servicosLegados(): array
    {
        return [
            'Server → /servers' => ['Server', '', '/servers'],
            'Auth login → /login' => ['Auth', 'login', '/login'],
            'Plan all → /plans/all' => ['Plan', 'all', '/plans/all'],
            'Plan me → /plan' => ['Plan', 'me', '/plan'],
            'Profile → /profile' => ['Profile', '', '/profile'],
            'Device store → /devices/store' => ['Device', 'store', '/devices/store'],
            'Vehicles dados → /vehicles/dados' => ['Vehicles', 'dados', '/vehicles/dados'],
            'Correios rastreio → /correios/rastreio' => ['Correios', 'rastreio', '/correios/rastreio'],
            'CNPJ cnpj → /dados/cnpj' => ['CNPJ', 'cnpj', '/dados/cnpj'],
            'CEP cep → /cep/cep' => ['CEP', 'cep', '/cep/cep'],
            'HoliDays feriados → /holidays/feriados' => ['HoliDays', 'feriados', '/holidays/feriados'],
            'DDD ddd → /ddd/ddd' => ['DDD', 'ddd', '/ddd/ddd'],
        ];
    }

    public function test_metodo_get_e_query_string_sao_respeitados(): void
    {
        $options = $this->handler([new Response(200, [], (string) json_encode(['ok' => true]))]);

        Service::Device('show', array_merge($options, [
            'Bearer' => 'jwt',
            'method' => 'GET',
            'query' => ['search' => 'dev-1'],
        ]));

        $request = $this->lastRequest();

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('search=dev-1', $request->getUri()->getQuery());
    }

    public function test_erros_http_voltam_como_corpo_sem_lancar_excecao(): void
    {
        $options = $this->handler([
            new Response(400, [], (string) json_encode(['error' => true, 'message' => 'Requisição inválida'])),
        ]);

        $response = Service::CEP('cep', array_merge($options, ['body' => ['cep' => 'x']]));

        $this->assertIsObject($response);
        $this->assertTrue($response->error);
        $this->assertSame('Requisição inválida', $response->message);
    }

    public function test_falha_de_rede_vira_objeto_de_erro(): void
    {
        $options = $this->handler([new \RuntimeException('conexão recusada')]);

        $response = Service::CEP('cep', array_merge($options, ['body' => ['cep' => 'x']]));

        $this->assertIsObject($response);
        $this->assertTrue($response->error);
        $this->assertSame('conexão recusada', $response->message);
    }

    public function test_vehicles_base_normaliza_o_tipo_como_chave_principal(): void
    {
        $options = $this->handler([new Response(200, [], (string) json_encode(['ok' => true]))]);

        Service::Vehicles('base/000/dados', array_merge($options, [
            'Bearer' => 'jwt',
            'body' => ['fipe' => ['placa' => 'ABC1234', 'homolog' => false]],
        ]));

        $this->assertEquals(
            ['tipo' => 'fipe', 'placa' => 'ABC1234', 'homolog' => false],
            json_decode((string) $this->lastRequest()->getBody(), true)
        );
    }
}
