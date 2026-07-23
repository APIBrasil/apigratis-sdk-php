<?php

namespace ApiBrasil;

/**
 * Interface legada da SDK: métodos estáticos por serviço, sempre com o
 * mesmo contrato (`['Bearer' => ..., 'DeviceToken' => ..., 'body' => [...]]`)
 * e resposta decodificada como `stdClass` — erros HTTP nunca lançam exceção.
 *
 * @deprecated Prefira `new \ApiBrasil\ApiBrasil([...])`, que cobre toda a
 *             plataforma (WhatsApp, SMS, consultas, pagamentos, relatórios)
 *             com métodos dedicados, erros tipados, retry e hooks.
 *             Esta classe continua funcionando por compatibilidade.
 *
 * @see \ApiBrasil\ApiBrasil
 */
class Service extends Base
{
    private const BASE_URI = 'https://gateway.apibrasil.io/api/v2/';

    private static function buildHeaders(array $data, array $extraHeaders = []): array
    {
        $headers = $extraHeaders;

        if (isset($data['Bearer']) && $data['Bearer'] !== '') {
            $headers['Authorization'] = 'Bearer '.$data['Bearer'];
        }

        if (isset($data['DeviceToken']) && $data['DeviceToken'] !== '') {
            $headers['DeviceToken'] = $data['DeviceToken'];
        }

        if (isset($data['SecretKey']) && $data['SecretKey'] !== '') {
            $headers['SecretKey'] = $data['SecretKey'];
        }

        return $headers;
    }

    private static function buildOptions(array $data): array
    {
        $options = [];

        if (isset($data['timeout'])) {
            $options['timeout'] = (int) $data['timeout'];
        }

        if (isset($data['connect_timeout'])) {
            $options['connect_timeout'] = (int) $data['connect_timeout'];
        }

        if (array_key_exists('verify', $data)) {
            $options['verify'] = (bool) $data['verify'];
        }

        if (isset($data['query']) && is_array($data['query'])) {
            $options['query'] = $data['query'];
        }

        // handler stack do Guzzle (MockHandler nos testes, proxies etc.)
        if (isset($data['handler'])) {
            $options['handler'] = $data['handler'];
        }

        return $options;
    }

    private static function call(string $path, array $data = [], array $extraHeaders = [], ?array $body = null)
    {
        $method = $data['method'] ?? 'POST';
        $headers = self::buildHeaders($data, $extraHeaders);
        $body ??= ($data['body'] ?? []);
        $options = self::buildOptions($data);

        return self::defaultRequest($method, self::BASE_URI, ltrim($path, '/'), $headers, $body, $options);
    }

    private static function normalizeTipoBody(array $body): array
    {
        if (array_key_exists('tipo', $body)) {
            return $body;
        }

        if (count($body) !== 1) {
            return $body;
        }

        $tipo = array_key_first($body);
        if (!is_string($tipo) || $tipo === '') {
            return $body;
        }

        $payload = $body[$tipo];
        if (!is_array($payload)) {
            return $body;
        }

        return ['tipo' => $tipo] + $payload;
    }

    public static function Server(array $data = [])
    {
        return self::call('servers', $data);
    }

    public static function Auth(string $action = '', array $data = [])
    {
        return self::call($action, $data);
    }

    public static function Plan(string $action = '', array $data = [])
    {
        if ($action === 'me') {
            return self::call('plan', $data);
        }

        $path = 'plans';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function Profile(array $data = [])
    {
        return self::call('profile', $data);
    }

    public static function Device(string $action = '', array $data = [])
    {
        $path = 'devices';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function WhatsApp(string $action = '', array $data = [])
    {
        $path = 'whatsapp';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function Vehicles(string $action = '', array $data = [])
    {
        $path = 'vehicles';
        $actionNormalized = ltrim($action, '/');
        if ($actionNormalized !== '') {
            $path .= '/'.$actionNormalized;
        }

        $body = $data['body'] ?? [];

        if (isset($data['tipo']) && !array_key_exists('tipo', $body)) {
            $body = ['tipo' => (string) $data['tipo']] + $body;
        }

        if (str_starts_with($actionNormalized, 'base/')) {
            $body = self::normalizeTipoBody($body);
        }

        return self::call($path, $data, [], $body);
    }

    public static function Correios(string $action = '', array $data = [])
    {
        $path = 'correios';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function CNPJ(string $action = '', array $data = [])
    {
        $path = 'dados';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function CEP(string $action = '', array $data = [])
    {
        $path = 'cep';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function HoliDays(string $action = '', array $data = [])
    {
        $path = 'holidays';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }

    public static function DDD(string $action = '', array $data = [])
    {
        $path = 'ddd';
        if ($action !== '') {
            $path .= '/'.ltrim($action, '/');
        }

        return self::call($path, $data);
    }
}

