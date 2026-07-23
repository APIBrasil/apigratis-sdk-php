<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Messaging;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/**
 * API de SMS (device-based): `POST /sms/{action}`.
 * Exige `Authorization: Bearer` + `DeviceToken`.
 */
class SmsService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'sms');
    }

    /**
     * Envia um SMS pelo device: `POST /sms/send`.
     *
     * Campos: `number`, `message`, `operator`, `user_reply`, `webhook_url`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function send(array $body, array $options = [])
    {
        return $this->request('send', $body, $options);
    }

    /**
     * Envia um SMS debitando créditos da conta (sem DeviceToken):
     * `POST /sms/send/credits`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function sendWithCredits(array $body, array $options = [])
    {
        return $this->http->post('/sms/send/credits', $body, $options);
    }
}
