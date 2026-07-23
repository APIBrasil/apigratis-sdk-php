<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\ApiBrasil;
use ApiBrasil\Core\Errors\ApiBrasilError;
use ApiBrasil\Test\Helpers\ApiTestCase;
use ApiBrasil\Test\Helpers\FakeTransport;

class ApiBrasilTest extends ApiTestCase
{
    public function test_expoe_todos_os_modulos_da_plataforma(): void
    {
        $modulos = [
            'auth', 'devices', 'whatsapp', 'evolution', 'whatsmeow', 'sms',
            'dados', 'vehicles', 'fipe', 'correios', 'cep', 'geolocation',
            'geomatrix', 'recognize', 'ddd', 'holidays', 'translate', 'weather',
            'loterias', 'databaseIp', 'consulta', 'ura', 'chipVirtual', 'bulk',
            'catalog', 'account', 'payments', 'ipWhitelist', 'bearerRateLimit',
            'reports',
        ];

        [, $api] = $this->buildApi();

        foreach ($modulos as $modulo) {
            $this->assertTrue(
                property_exists($api, $modulo) && isset($api->{$modulo}),
                "módulo {$modulo} não foi inicializado"
            );
        }
    }

    public function test_request_generico_repassa_metodo_path_body_e_query(): void
    {
        [$transport, $api] = $this->buildApi();

        $api->request('PATCH', '/notifications/9/read');
        $this->assertSame('PATCH', $transport->last()->method);
        $this->assertSame(self::BASE.'/notifications/9/read', $transport->last()->url);

        $api->request('GET', '/reports/quick-stats', null, ['query' => ['period' => '7d']]);
        $this->assertSame(self::BASE.'/reports/quick-stats?period=7d', $transport->last()->url);
    }

    public function test_set_bearer_token_e_set_device_token_sao_encadeaveis(): void
    {
        [$transport, $api] = $this->buildApi();

        $api->setBearerToken('jwt-2')->setDeviceToken('dev-2');
        $api->whatsapp->qrcode();

        $this->assertSame('Bearer jwt-2', $transport->lastHeaders()['Authorization']);
        $this->assertSame('dev-2', $transport->lastHeaders()['DeviceToken']);
    }

    public function test_with_device_cria_cliente_com_outro_device_token_e_mesmo_transporte(): void
    {
        [$transport, $api] = $this->buildApi();

        $outro = $api->withDevice('outro-device');
        $outro->whatsapp->qrcode();

        $this->assertSame('outro-device', $transport->lastHeaders()['DeviceToken']);
        $this->assertSame('Bearer jwt', $transport->lastHeaders()['Authorization']);
    }

    public function test_login_guarda_o_bearer_token_retornado(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::ok(['authorization' => ['token' => 'jwt-novo'], 'user' => ['id' => 1]])
        );
        $api = new ApiBrasil(['transport' => $transport, 'retry' => false, 'baseURL' => self::BASE]);

        $api->auth->login(['email' => 'a@b.c', 'password' => 'x']);
        $api->account->balance();

        $this->assertSame('Bearer jwt-novo', $transport->lastHeaders()['Authorization']);
    }

    public function test_login_com_2fa_nao_guarda_token_e_retorna_challenge(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::ok(['requires_2fa' => true, 'challenge' => 'ch-123', 'available_methods' => ['email']])
        );
        $api = new ApiBrasil(['transport' => $transport, 'retry' => false, 'baseURL' => self::BASE]);

        $session = $api->auth->login(['email' => 'a@b.c', 'password' => 'x']);

        $this->assertTrue($session['requires_2fa']);
        $this->assertSame('ch-123', $session['challenge']);

        $api->account->balance();
        $this->assertArrayNotHasKey('Authorization', $transport->lastHeaders());
    }

    public function test_verify2fa_guarda_o_token_apos_o_desafio(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::ok(['authorization' => ['token' => 'jwt-2fa']])
        );
        $api = new ApiBrasil(['transport' => $transport, 'retry' => false, 'baseURL' => self::BASE]);

        $api->auth->verify2fa(['challenge' => 'ch-123', 'code' => '000000']);
        $api->account->balance();

        $this->assertSame('Bearer jwt-2fa', $transport->lastHeaders()['Authorization']);
    }

    public function test_api_brasil_login_retorna_cliente_autenticado(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::ok(['authorization' => ['token' => 'jwt-static']])
        );

        ['client' => $client, 'session' => $session] = ApiBrasil::login(
            ['email' => 'a@b.c', 'password' => 'x'],
            ['transport' => $transport, 'retry' => false, 'baseURL' => self::BASE]
        );

        $this->assertSame('jwt-static', $session['authorization']['token']);

        $client->account->balance();
        $this->assertSame('Bearer jwt-static', $transport->lastHeaders()['Authorization']);
    }

    public function test_api_brasil_login_lanca_erro_quando_a_conta_exige_2fa(): void
    {
        $transport = (new FakeTransport())->respondWith(
            FakeTransport::ok(['requires_2fa' => true, 'challenge' => 'ch-1'])
        );

        $this->expectException(ApiBrasilError::class);
        ApiBrasil::login(
            ['email' => 'a@b.c', 'password' => 'x'],
            ['transport' => $transport, 'retry' => false, 'baseURL' => self::BASE]
        );
    }
}
