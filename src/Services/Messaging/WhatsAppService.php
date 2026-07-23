<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Messaging;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/**
 * API de WhatsApp (device-based): `POST /whatsapp/{action}`.
 * Exige `Authorization: Bearer` + `DeviceToken`.
 *
 * Além dos métodos nomeados, `request($action, $body)` aceita qualquer
 * action do catálogo (`ApiBrasil\Generated\Catalog::WHATSAPP_ACTIONS`).
 */
class WhatsAppService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'whatsapp');
    }

    /**
     * Inicia a sessão do device (aceita webhooks opcionais):
     * `webhook_wh_status`, `webhook_wh_message`, `webhook_wh_connect`,
     * `webhook_wh_qrcode`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function start(?array $body = null, array $options = [])
    {
        return $this->request('start', $body, $options);
    }

    /**
     * Retorna o QR Code de pareamento (`response.qrcode` em base64).
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function qrcode(?array $body = null, array $options = [])
    {
        return $this->request('qrcode', $body, $options);
    }

    /**
     * Encerra a sessão.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function logout(?array $body = null, array $options = [])
    {
        return $this->request('logout', $body, $options);
    }

    /**
     * Fecha o navegador/sessão no servidor.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function close(?array $body = null, array $options = [])
    {
        return $this->request('close', $body, $options);
    }

    /**
     * Apaga a sessão no servidor.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function deleteSession(?array $body = null, array $options = [])
    {
        return $this->request('deleteSession', $body, $options);
    }

    /**
     * Envia mensagem de texto: `['number' => '5511999999999', 'text' => 'Olá!']`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendText(array $body, array $options = [])
    {
        return $this->request('sendText', $body, $options);
    }

    /**
     * Envia arquivo a partir de uma URL: `['number' => ..., 'path' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendFile(array $body, array $options = [])
    {
        return $this->request('sendFile', $body, $options);
    }

    /**
     * Envia arquivo em base64.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendFile64(array $body, array $options = [])
    {
        return $this->request('sendFile64', $body, $options);
    }

    /**
     * Envia áudio (URL; convertido para mp3 pelo gateway, máx. 6 min).
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendAudio(array $body, array $options = [])
    {
        return $this->request('sendAudio', $body, $options);
    }

    /**
     * Envia vídeo a partir de uma URL.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendVideo(array $body, array $options = [])
    {
        return $this->request('sendVideo', $body, $options);
    }

    /**
     * Envia um link com preview.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendLink(array $body, array $options = [])
    {
        return $this->request('sendLink', $body, $options);
    }

    /**
     * Envia uma localização: `['number' => ..., 'lat' => ..., 'lng' => ...]`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendLocation(array $body, array $options = [])
    {
        return $this->request('sendLocation', $body, $options);
    }

    /**
     * Envia um contato.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendContact(array $body, array $options = [])
    {
        return $this->request('sendContact', $body, $options);
    }

    /**
     * Executa qualquer action de forma assíncrona via fila:
     * `POST /whatsapp/{action}/queue`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function queue(string $action, ?array $body = null, array $options = [])
    {
        return $this->http->post('/whatsapp/'.trim($action, '/').'/queue', $body, $options);
    }
}
