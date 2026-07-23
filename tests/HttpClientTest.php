<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\Core\Errors\AuthenticationError;
use ApiBrasil\Core\Errors\InsufficientBalanceError;
use ApiBrasil\Core\Errors\NotFoundError;
use ApiBrasil\Core\Errors\ValidationError;
use ApiBrasil\Core\HttpClient;
use ApiBrasil\Test\Helpers\FakeTransport;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    /**
     * @param array<string,mixed> $config
     */
    private function client(FakeTransport $transport, array $config = []): HttpClient
    {
        return new HttpClient(array_merge([
            'transport' => $transport,
            'retry' => false,
        ], $config));
    }

    protected function tearDown(): void
    {
        foreach (['APIBRASIL_BEARER_TOKEN', 'APIBRASIL_DEVICE_TOKEN', 'APIBRASIL_BASE_URL'] as $name) {
            putenv($name);
            unset($_ENV[$name], $_SERVER[$name]);
        }

        parent::tearDown();
    }

    public function test_monta_a_url_com_a_base_padrao_e_envia_os_headers_da_plataforma(): void
    {
        $transport = new FakeTransport();
        $this->client($transport)->post('/whatsapp/sendText', ['number' => '55']);

        $this->assertSame(HttpClient::DEFAULT_BASE_URL.'/whatsapp/sendText', $transport->last()->url);
        $this->assertSame('POST', $transport->last()->method);
        $this->assertSame('application/json', $transport->lastHeaders()['Content-Type']);
        $this->assertSame('application/json', $transport->lastHeaders()['Accept']);
        $this->assertSame(HttpClient::SDK_USER_AGENT, $transport->lastHeaders()['User-Agent']);
        $this->assertEquals(['number' => '55'], $transport->lastBody());
    }

    public function test_respeita_base_url_customizada_e_monta_query_string(): void
    {
        $transport = new FakeTransport();
        $http = $this->client($transport, ['baseURL' => 'https://homolog.example.com/api/v2/']);

        $http->get('/apis', ['query' => ['search' => 'whatsapp', 'page' => 2, 'skip' => null]]);

        $this->assertSame(
            'https://homolog.example.com/api/v2/apis?search=whatsapp&page=2',
            $transport->last()->url
        );
    }

    public function test_injeta_authorization_devicetoken_e_secretkey_quando_configurados(): void
    {
        $transport = new FakeTransport();
        $http = $this->client($transport, ['bearerToken' => 'jwt-123', 'deviceToken' => 'dev-456']);

        $http->post('/devices/store', [], ['secretKey' => 'sk-789']);

        $this->assertSame('Bearer jwt-123', $transport->lastHeaders()['Authorization']);
        $this->assertSame('dev-456', $transport->lastHeaders()['DeviceToken']);
        $this->assertSame('sk-789', $transport->lastHeaders()['SecretKey']);
    }

    public function test_nao_injeta_headers_de_auth_quando_nao_configurados(): void
    {
        $transport = new FakeTransport();
        $this->client($transport)->get('/apis');

        $this->assertArrayNotHasKey('Authorization', $transport->lastHeaders());
        $this->assertArrayNotHasKey('DeviceToken', $transport->lastHeaders());
        $this->assertArrayNotHasKey('SecretKey', $transport->lastHeaders());
    }

    public function test_permite_sobrescrever_tokens_por_requisicao(): void
    {
        $transport = new FakeTransport();
        $http = $this->client($transport, ['bearerToken' => 'global', 'deviceToken' => 'global-dev']);

        $http->post('/sms/send', [], ['bearerToken' => 'local', 'deviceToken' => 'local-dev']);

        $this->assertSame('Bearer local', $transport->lastHeaders()['Authorization']);
        $this->assertSame('local-dev', $transport->lastHeaders()['DeviceToken']);
    }

    public function test_get_nao_envia_body(): void
    {
        $transport = new FakeTransport();
        $this->client($transport)->get('/balance');

        $this->assertNull($transport->last()->body);
    }

    public function test_body_vazio_vira_objeto_json(): void
    {
        $transport = new FakeTransport();
        $this->client($transport)->post('/whatsapp/qrcode', []);

        $this->assertSame('{}', $transport->last()->body);
    }

    public function test_headers_extras_da_configuracao_sao_enviados(): void
    {
        $transport = new FakeTransport();
        $this->client($transport, ['headers' => ['X-Custom' => 'valor']])->get('/status');

        $this->assertSame('valor', $transport->lastHeaders()['X-Custom']);
    }

    public function test_set_bearer_token_e_set_device_token_atualizam_requisicoes_futuras(): void
    {
        $transport = new FakeTransport();
        $http = $this->client($transport);
        $http->setBearerToken('novo-jwt');
        $http->setDeviceToken('novo-dev');
        $http->get('/profile/me');

        $this->assertSame('Bearer novo-jwt', $transport->lastHeaders()['Authorization']);
        $this->assertSame('novo-dev', $transport->lastHeaders()['DeviceToken']);
    }

    public function test_mapeia_erros_para_as_subclasses_corretas(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::httpError(402, [
                'error' => true,
                'message' => 'Saldo insuficiente',
                'code' => 'NO_BALANCE',
            ]),
            FakeTransport::httpError(401, ['message' => 'Token inválido']),
            FakeTransport::httpError(422, ['message' => 'Placa inválida']),
            FakeTransport::httpError(404, ['message' => 'Sem dados'])
        );
        $http = $this->client($transport, ['bearerToken' => 'jwt']);

        try {
            $http->post('/consulta/cpf/credits', []);
            $this->fail('Deveria ter lançado InsufficientBalanceError.');
        } catch (InsufficientBalanceError $error) {
            $this->assertSame('Saldo insuficiente', $error->getMessage());
            $this->assertSame(402, $error->getStatus());
            $this->assertSame('NO_BALANCE', $error->getErrorCode());
            $this->assertTrue($error->isInsufficientBalance());
        }

        $this->expectException(AuthenticationError::class);
        $http->get('/profile/me');
    }

    public function test_mapeia_422_para_validation_error(): void
    {
        $transport = (new FakeTransport())->setFallback(
            FakeTransport::httpError(422, ['message' => 'Placa inválida'])
        );

        $this->expectException(ValidationError::class);
        $this->client($transport)->post('/x', []);
    }

    public function test_mapeia_404_para_not_found_error(): void
    {
        $transport = (new FakeTransport())->setFallback(
            FakeTransport::httpError(404, ['message' => 'Sem dados'])
        );

        $this->expectException(NotFoundError::class);
        $this->client($transport)->post('/y', []);
    }

    public function test_le_credenciais_das_variaveis_de_ambiente_quando_nao_configuradas(): void
    {
        putenv('APIBRASIL_BEARER_TOKEN=env-jwt');
        putenv('APIBRASIL_DEVICE_TOKEN=env-dev');
        putenv('APIBRASIL_BASE_URL=https://env.example.com/api/v2');

        $transport = new FakeTransport();
        $this->client($transport)->post('/sms/send', []);

        $this->assertSame('https://env.example.com/api/v2/sms/send', $transport->last()->url);
        $this->assertSame('Bearer env-jwt', $transport->lastHeaders()['Authorization']);
        $this->assertSame('env-dev', $transport->lastHeaders()['DeviceToken']);
    }

    public function test_configuracao_explicita_tem_prioridade_sobre_o_ambiente(): void
    {
        putenv('APIBRASIL_BEARER_TOKEN=env-jwt');

        $transport = new FakeTransport();
        $this->client($transport, ['bearerToken' => 'explicito'])->get('/balance');

        $this->assertSame('Bearer explicito', $transport->lastHeaders()['Authorization']);
    }

    public function test_expoe_baseurl_tokens_e_secretkey_configurados(): void
    {
        $http = new HttpClient([
            'baseURL' => 'https://example.com/api/v2',
            'bearerToken' => 'jwt',
            'deviceToken' => 'dev',
            'secretKey' => 'sk',
            'retry' => false,
        ]);

        $this->assertSame('https://example.com/api/v2', $http->getBaseUrl());
        $this->assertSame('jwt', $http->getBearerToken());
        $this->assertSame('dev', $http->getDeviceToken());
        $this->assertSame('sk', $http->getSecretKey());
        $this->assertSame('jwt', $http->getConfig()['bearerToken']);
        $this->assertSame('sk', $http->getConfig()['secretKey']);
    }
}
