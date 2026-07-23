<?php

declare(strict_types=1);

namespace ApiBrasil\Test\Contract;

use ApiBrasil\ApiBrasil;
use PHPUnit\Framework\TestCase;

/**
 * Testes de contrato contra o gateway real.
 *
 * Desativados por padrão — rode com:
 *   composer test:contract
 *
 * Variáveis opcionais:
 *   APIBRASIL_BASE_URL      — outra base (ex: homolog)
 *   APIBRASIL_BEARER_TOKEN  — habilita os testes autenticados
 *
 * Somente endpoints públicos/gratuitos são chamados — nenhuma consulta
 * que debita créditos.
 *
 * @group contract
 */
class GatewayTest extends TestCase
{
    private ApiBrasil $api;

    protected function setUp(): void
    {
        parent::setUp();

        if (getenv('APIBRASIL_CONTRACT') !== '1') {
            $this->markTestSkipped('Defina APIBRASIL_CONTRACT=1 para rodar os testes de contrato.');
        }

        $this->api = new ApiBrasil(['retry' => false, 'timeout' => 60000]);
    }

    public function test_documentations_responde_com_o_catalogo(): void
    {
        $response = $this->api->catalog->documentations();
        $docs = $response['documentations'] ?? $response;

        $this->assertIsArray($docs);
        $this->assertNotEmpty($docs);
        $this->assertArrayHasKey('endpoints', $docs[0]);
    }

    public function test_apis_responde_com_a_lista_de_apis(): void
    {
        $response = $this->api->catalog->apis();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('apis', $response);
    }

    public function test_plans_responde(): void
    {
        $this->assertNotNull($this->api->catalog->plans());
    }

    public function test_profile_me_autentica_com_o_bearer_token(): void
    {
        $bearer = getenv('APIBRASIL_BEARER_TOKEN');
        if ($bearer === false || $bearer === '') {
            $this->markTestSkipped('Defina APIBRASIL_BEARER_TOKEN para os testes autenticados.');
        }

        $authed = new ApiBrasil(['bearerToken' => $bearer, 'retry' => false]);

        $this->assertNotNull($authed->auth->me());
    }

    public function test_balance_retorna_o_saldo(): void
    {
        $bearer = getenv('APIBRASIL_BEARER_TOKEN');
        if ($bearer === false || $bearer === '') {
            $this->markTestSkipped('Defina APIBRASIL_BEARER_TOKEN para os testes autenticados.');
        }

        $authed = new ApiBrasil(['bearerToken' => $bearer, 'retry' => false]);

        $this->assertNotNull($authed->account->balance());
    }
}
