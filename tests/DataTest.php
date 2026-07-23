<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\ApiBrasil;
use ApiBrasil\Generated\Catalog;
use ApiBrasil\Test\Helpers\ApiTestCase;

class DataTest extends ApiTestCase
{
    /**
     * @dataProvider dadosCases
     * @dataProvider vehiclesCases
     * @dataProvider fipeCorreiosCepCases
     * @dataProvider consultaCases
     * @dataProvider uraChipBulkCases
     * @dataProvider proxyCases
     *
     * @param callable(ApiBrasil):mixed $call
     * @param array<string,mixed>|null  $body
     */
    public function test_rota(callable $call, string $path, string $method = 'POST', ?array $body = null): void
    {
        $this->assertRoute($call, $path, $method, $body);
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function dadosCases(): array
    {
        return [
            'dados.cnpj → POST /dados/cnpj' => [
                static fn (ApiBrasil $api) => $api->dados->cnpj(['cnpj' => '00000000000000']),
                '/dados/cnpj',
                'POST',
                ['cnpj' => '00000000000000'],
            ],
            'dados.cpf → POST /dados/cpf' => [
                static fn (ApiBrasil $api) => $api->dados->cpf(['cpf' => '00000000000']),
                '/dados/cpf',
                'POST',
                ['cpf' => '00000000000'],
            ],
            'dados.request genérico → POST /dados/{action}' => [
                static fn (ApiBrasil $api) => $api->dados->request('telefone', ['telefone' => '11999999999']),
                '/dados/telefone',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function vehiclesCases(): array
    {
        return [
            'vehicles.dados → POST /vehicles/dados' => [
                static fn (ApiBrasil $api) => $api->vehicles->dados(['placa' => 'ABC1234']),
                '/vehicles/dados',
                'POST',
                ['placa' => 'ABC1234'],
            ],
            'vehicles.fipe → POST /vehicles/fipe' => [
                static fn (ApiBrasil $api) => $api->vehicles->fipe(['placa' => 'ABC1234']),
                '/vehicles/fipe',
                'POST',
                ['placa' => 'ABC1234'],
            ],
            'vehicles.consultaFipe → POST /vehicles/consultafipe/{placa}' => [
                static fn (ApiBrasil $api) => $api->vehicles->consultaFipe('ABC1234'),
                '/vehicles/consultafipe/ABC1234',
                'POST',
                null,
            ],
            'vehicles.request genérico → POST /vehicles/{action}' => [
                static fn (ApiBrasil $api) => $api->vehicles->request('leilao', ['placa' => 'ABC1234']),
                '/vehicles/leilao',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function fipeCorreiosCepCases(): array
    {
        return [
            'fipe.request → POST /fipe/{action}' => [
                static fn (ApiBrasil $api) => $api->fipe->request('ConsultarMarcas', ['tipo' => 'carros']),
                '/fipe/ConsultarMarcas',
                'POST',
                ['tipo' => 'carros'],
            ],
            'correios.rastreio → POST /correios/rastreio' => [
                static fn (ApiBrasil $api) => $api->correios->rastreio(['codigo' => 'BR123456789BR']),
                '/correios/rastreio',
                'POST',
                ['codigo' => 'BR123456789BR'],
            ],
            'correios.request → POST /correios/{action}' => [
                static fn (ApiBrasil $api) => $api->correios->request('frete', ['cepOrigem' => '01001000']),
                '/correios/frete',
                'POST',
                null,
            ],
            'cep.cep → POST /cep/cep' => [
                static fn (ApiBrasil $api) => $api->cep->cep(['cep' => '01001000']),
                '/cep/cep',
                'POST',
                ['cep' => '01001000'],
            ],
            'cep.request → POST /cep/{action}' => [
                static fn (ApiBrasil $api) => $api->cep->request('geolocation', ['cep' => '01001000']),
                '/cep/geolocation',
                'POST',
                null,
            ],
            'databaseIp.ip → POST /database/ip' => [
                static fn (ApiBrasil $api) => $api->databaseIp->ip(['ip' => '8.8.8.8']),
                '/database/ip',
                'POST',
                ['ip' => '8.8.8.8'],
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function consultaCases(): array
    {
        return [
            'consulta.generic → POST /consulta/{service}/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->generic('serasa-score', ['cpf' => '000']),
                '/consulta/serasa-score/credits',
                'POST',
                ['cpf' => '000'],
            ],
            'consulta.cpf → POST /consulta/cpf/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->cpf(['cpf' => '00000000000', 'homolog' => true]),
                '/consulta/cpf/credits',
                'POST',
                ['cpf' => '00000000000', 'homolog' => true],
            ],
            'consulta.cnpj → POST /consulta/cnpj/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->cnpj(['cnpj' => '00000000000000', 'tipo' => 'lista-socios']),
                '/consulta/cnpj/credits',
                'POST',
                ['cnpj' => '00000000000000', 'tipo' => 'lista-socios'],
            ],
            'consulta.cnh → POST /consulta/cnh/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->cnh(['cpf' => '00000000000']),
                '/consulta/cnh/credits',
                'POST',
                null,
            ],
            'consulta.cep → POST /consulta/cep/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->cep(['cep' => '01001000']),
                '/consulta/cep/credits',
                'POST',
                null,
            ],
            'consulta.veiculos → POST /consulta/veiculos/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->veiculos(['placa' => 'ABC1234']),
                '/consulta/veiculos/credits',
                'POST',
                ['placa' => 'ABC1234'],
            ],
            'consulta.telefone → POST /consulta/telefone/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->telefone(['telefone' => '11999999999']),
                '/consulta/telefone/credits',
                'POST',
                null,
            ],
            'consulta.veiculosBase dados → POST /vehicles/base/000/dados' => [
                static fn (ApiBrasil $api) => $api->consulta->veiculosBase('dados', ['placa' => 'ABC1234']),
                '/vehicles/base/000/dados',
                'POST',
                null,
            ],
            'consulta.veiculosBase fipe → POST /vehicles/base/000/fipe' => [
                static fn (ApiBrasil $api) => $api->consulta->veiculosBase('fipe', ['placa' => 'ABC1234']),
                '/vehicles/base/000/fipe',
                'POST',
                null,
            ],
            'consulta.cepDistancia → POST /cep/distancia/calcular' => [
                static fn (ApiBrasil $api) => $api->consulta->cepDistancia(['cep_origem' => '01001000']),
                '/cep/distancia/calcular',
                'POST',
                null,
            ],
            'consulta.proxySeller → POST /proxy/seller/credits' => [
                static fn (ApiBrasil $api) => $api->consulta->proxySeller([]),
                '/proxy/seller/credits',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function uraChipBulkCases(): array
    {
        return [
            'ura.dialler → POST /ura/call/dialler' => [
                static fn (ApiBrasil $api) => $api->ura->dialler(['number' => '11999999999']),
                '/ura/call/dialler',
                'POST',
                null,
            ],
            'ura.status → POST /ura/call/status' => [
                static fn (ApiBrasil $api) => $api->ura->status(['id' => 'call-1']),
                '/ura/call/status',
                'POST',
                null,
            ],
            'chipVirtual.operators → POST /chip/virtual/operators' => [
                static fn (ApiBrasil $api) => $api->chipVirtual->operators(),
                '/chip/virtual/operators',
                'POST',
                null,
            ],
            'chipVirtual.buy → POST /chip/virtual/buy' => [
                static fn (ApiBrasil $api) => $api->chipVirtual->buy(['operator' => 'claro']),
                '/chip/virtual/buy',
                'POST',
                null,
            ],
            'chipVirtual.activation → POST /chip/virtual/activation' => [
                static fn (ApiBrasil $api) => $api->chipVirtual->activation(['id' => 1]),
                '/chip/virtual/activation',
                'POST',
                null,
            ],
            'chipVirtual.services → POST /chip/virtual/services' => [
                static fn (ApiBrasil $api) => $api->chipVirtual->services(),
                '/chip/virtual/services',
                'POST',
                null,
            ],
            'bulk.direct → POST /bulk/direct/{action}' => [
                static fn (ApiBrasil $api) => $api->bulk->direct('sms', ['items' => []]),
                '/bulk/direct/sms',
                'POST',
                ['items' => []],
            ],
            'bulk.queue → POST /bulk/queue/{action}' => [
                static fn (ApiBrasil $api) => $api->bulk->queue('whatsapp', ['items' => []]),
                '/bulk/queue/whatsapp',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function proxyCases(): array
    {
        return [
            'geolocation → POST /geolocation/{action}' => [
                static fn (ApiBrasil $api) => $api->geolocation->request('geocode', ['address' => 'Av. Paulista']),
                '/geolocation/geocode',
                'POST',
                null,
            ],
            'geomatrix → POST /geomatrix/{action}' => [
                static fn (ApiBrasil $api) => $api->geomatrix->request('distance', []),
                '/geomatrix/distance',
                'POST',
                null,
            ],
            'recognize → POST /recognize/{action}' => [
                static fn (ApiBrasil $api) => $api->recognize->request('identify', ['base64' => 'x']),
                '/recognize/identify',
                'POST',
                null,
            ],
            'ddd → POST /ddd/{action}' => [
                static fn (ApiBrasil $api) => $api->ddd->request('cidades', ['ddd' => '11']),
                '/ddd/cidades',
                'POST',
                null,
            ],
            'holidays → POST /holidays/{action}' => [
                static fn (ApiBrasil $api) => $api->holidays->request('feriados', ['ano' => '2026']),
                '/holidays/feriados',
                'POST',
                null,
            ],
            'translate → POST /translate/{action}' => [
                static fn (ApiBrasil $api) => $api->translate->request('translate', ['text' => 'hello']),
                '/translate/translate',
                'POST',
                null,
            ],
            'weather → POST /weather/{action}' => [
                static fn (ApiBrasil $api) => $api->weather->request('previsao', ['cidade' => 'São Paulo']),
                '/weather/previsao',
                'POST',
                null,
            ],
            'loterias → POST /loterias/{action}' => [
                static fn (ApiBrasil $api) => $api->loterias->request('megasena', []),
                '/loterias/megasena',
                'POST',
                null,
            ],
        ];
    }

    /**
     * @dataProvider proxyServicos
     */
    public function test_cobre_todas_as_actions_documentadas_do_servico(string $service): void
    {
        $actions = Catalog::serviceActions($service);
        [$transport, $api] = $this->buildApi();

        foreach ($actions as $action) {
            $api->{$service}->request($action);
            $this->assertSame(self::BASE."/{$service}/{$action}", $transport->last()->url);
            $this->assertSame('POST', $transport->last()->method);
        }

        $this->assertCount(count($actions), $transport->calls);
    }

    /** @return array<string,array{0:string}> */
    public function proxyServicos(): array
    {
        $servicos = [
            'sms', 'dados', 'vehicles', 'fipe', 'correios', 'cep', 'ddd',
            'holidays', 'translate', 'weather', 'loterias', 'geolocation',
            'geomatrix', 'recognize',
        ];

        $cases = [];
        foreach ($servicos as $servico) {
            $cases[$servico] = [$servico];
        }

        return $cases;
    }

    public function test_cobre_todos_os_tipos_de_consulta_do_catalogo(): void
    {
        $tipos = Catalog::CONSULTA_TIPOS;
        $this->assertGreaterThan(200, count($tipos));

        [$transport, $api] = $this->buildApi();
        foreach ($tipos as $tipo => $meta) {
            $api->consulta->generic($meta['service'], ['tipo' => $tipo, 'homolog' => true]);
            $this->assertSame(
                self::BASE."/consulta/{$meta['service']}/credits",
                $transport->last()->url
            );
            $this->assertEquals(['tipo' => $tipo, 'homolog' => true], $transport->lastBody());
        }

        $this->assertCount(count($tipos), $transport->calls);
    }
}
