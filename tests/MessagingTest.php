<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\ApiBrasil;
use ApiBrasil\Generated\Catalog;
use ApiBrasil\Test\Helpers\ApiTestCase;

class MessagingTest extends ApiTestCase
{
    /**
     * @dataProvider whatsappCases
     * @dataProvider evolutionCases
     * @dataProvider whatsmeowCases
     * @dataProvider smsCases
     *
     * @param callable(ApiBrasil):mixed $call
     * @param array<string,mixed>|null  $body
     */
    public function test_rota(callable $call, string $path, string $method = 'POST', ?array $body = null): void
    {
        $this->assertRoute($call, $path, $method, $body);
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function whatsappCases(): array
    {
        return [
            'whatsapp.start → POST /whatsapp/start' => [
                static fn (ApiBrasil $api) => $api->whatsapp->start(['webhook_wh_message' => 'https://wh.example.com']),
                '/whatsapp/start',
                'POST',
                ['webhook_wh_message' => 'https://wh.example.com'],
            ],
            'whatsapp.qrcode → POST /whatsapp/qrcode' => [
                static fn (ApiBrasil $api) => $api->whatsapp->qrcode(),
                '/whatsapp/qrcode',
                'POST',
                null,
            ],
            'whatsapp.logout → POST /whatsapp/logout' => [
                static fn (ApiBrasil $api) => $api->whatsapp->logout(),
                '/whatsapp/logout',
                'POST',
                null,
            ],
            'whatsapp.close → POST /whatsapp/close' => [
                static fn (ApiBrasil $api) => $api->whatsapp->close(),
                '/whatsapp/close',
                'POST',
                null,
            ],
            'whatsapp.deleteSession → POST /whatsapp/deleteSession' => [
                static fn (ApiBrasil $api) => $api->whatsapp->deleteSession(),
                '/whatsapp/deleteSession',
                'POST',
                null,
            ],
            'whatsapp.sendText → POST /whatsapp/sendText' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendText(['number' => '5511999999999', 'text' => 'Olá']),
                '/whatsapp/sendText',
                'POST',
                ['number' => '5511999999999', 'text' => 'Olá'],
            ],
            'whatsapp.sendFile → POST /whatsapp/sendFile' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendFile(['number' => '55', 'path' => 'https://x.com/a.pdf']),
                '/whatsapp/sendFile',
                'POST',
                ['number' => '55', 'path' => 'https://x.com/a.pdf'],
            ],
            'whatsapp.sendFile64 → POST /whatsapp/sendFile64' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendFile64(['number' => '55', 'path' => 'data:...']),
                '/whatsapp/sendFile64',
                'POST',
                null,
            ],
            'whatsapp.sendAudio → POST /whatsapp/sendAudio' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendAudio(['number' => '55', 'path' => 'https://x.com/a.mp3']),
                '/whatsapp/sendAudio',
                'POST',
                null,
            ],
            'whatsapp.sendVideo → POST /whatsapp/sendVideo' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendVideo(['number' => '55', 'path' => 'https://x.com/a.mp4']),
                '/whatsapp/sendVideo',
                'POST',
                null,
            ],
            'whatsapp.sendLink → POST /whatsapp/sendLink' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendLink(['number' => '55', 'url' => 'https://x.com']),
                '/whatsapp/sendLink',
                'POST',
                null,
            ],
            'whatsapp.sendLocation → POST /whatsapp/sendLocation' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendLocation(['number' => '55', 'lat' => -23.5, 'lng' => -46.6]),
                '/whatsapp/sendLocation',
                'POST',
                ['number' => '55', 'lat' => -23.5, 'lng' => -46.6],
            ],
            'whatsapp.sendContact → POST /whatsapp/sendContact' => [
                static fn (ApiBrasil $api) => $api->whatsapp->sendContact(['number' => '55', 'contact' => '5511']),
                '/whatsapp/sendContact',
                'POST',
                null,
            ],
            'whatsapp.request genérico → POST /whatsapp/{action}' => [
                static fn (ApiBrasil $api) => $api->whatsapp->request('getAllChats', []),
                '/whatsapp/getAllChats',
                'POST',
                null,
            ],
            'whatsapp.queue → POST /whatsapp/{action}/queue' => [
                static fn (ApiBrasil $api) => $api->whatsapp->queue('sendText', ['number' => '55', 'text' => 'fila']),
                '/whatsapp/sendText/queue',
                'POST',
                ['number' => '55', 'text' => 'fila'],
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function evolutionCases(): array
    {
        return [
            'evolution.request → POST /evolution/{controller}/{action}' => [
                static fn (ApiBrasil $api) => $api->evolution->request('instance', 'create', ['instanceName' => 'b']),
                '/evolution/instance/create',
                'POST',
                ['instanceName' => 'b'],
            ],
            'evolution.call → POST /evolution/{path}' => [
                static fn (ApiBrasil $api) => $api->evolution->call('message/sendText', ['number' => '55']),
                '/evolution/message/sendText',
                'POST',
                ['number' => '55'],
            ],
            'evolution.queue → POST /evolution/{controller}/{action}/queue' => [
                static fn (ApiBrasil $api) => $api->evolution->queue('message', 'sendText', []),
                '/evolution/message/sendText/queue',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function whatsmeowCases(): array
    {
        return [
            'whatsmeow.request → POST /whatsmeow/{action}' => [
                static fn (ApiBrasil $api) => $api->whatsmeow->request('instance/connect', []),
                '/whatsmeow/instance/connect',
                'POST',
                null,
            ],
            'whatsmeow.queue → POST /whatsmeow/{action}/queue' => [
                static fn (ApiBrasil $api) => $api->whatsmeow->queue('instance/connect', []),
                '/whatsmeow/instance/connect/queue',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function smsCases(): array
    {
        return [
            'sms.send → POST /sms/send' => [
                static fn (ApiBrasil $api) => $api->sms->send(['number' => '5511999999999', 'message' => 'oi']),
                '/sms/send',
                'POST',
                ['number' => '5511999999999', 'message' => 'oi'],
            ],
            'sms.sendWithCredits → POST /sms/send/credits' => [
                static fn (ApiBrasil $api) => $api->sms->sendWithCredits(['number' => '55', 'message' => 'oi']),
                '/sms/send/credits',
                'POST',
                ['number' => '55', 'message' => 'oi'],
            ],
            'sms.request genérico → POST /sms/{action}' => [
                static fn (ApiBrasil $api) => $api->sms->request('status', ['id' => 1]),
                '/sms/status',
                'POST',
                null,
            ],
        ];
    }

    public function test_cobre_todas_as_actions_documentadas_do_whatsapp(): void
    {
        $actions = Catalog::serviceActions('whatsapp');
        $this->assertGreaterThan(100, count($actions));

        [$transport, $api] = $this->buildApi();
        foreach ($actions as $action) {
            $api->whatsapp->request($action);
            $this->assertSame(self::BASE."/whatsapp/{$action}", $transport->last()->url);
            $this->assertSame('POST', $transport->last()->method);
        }

        $this->assertCount(count($actions), $transport->calls);
    }

    public function test_cobre_todas_as_actions_documentadas_do_whatsmeow(): void
    {
        $actions = Catalog::serviceActions('whatsmeow');
        $this->assertGreaterThan(20, count($actions));

        [$transport, $api] = $this->buildApi();
        foreach ($actions as $action) {
            $api->whatsmeow->request($action);
            $this->assertSame(self::BASE."/whatsmeow/{$action}", $transport->last()->url);
        }
    }

    public function test_cobre_todos_os_caminhos_documentados_da_evolution(): void
    {
        $paths = Catalog::serviceActions('evolution');
        $this->assertGreaterThan(40, count($paths));

        [$transport, $api] = $this->buildApi();
        foreach ($paths as $path) {
            $api->evolution->call($path);
            $this->assertSame(self::BASE."/evolution/{$path}", $transport->last()->url);
        }
    }
}
