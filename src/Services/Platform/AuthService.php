<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Autenticação e conta (`/auth/*`, `/profile*`, `/password/*`).
 *
 * `login()` e `verify2fa()` guardam automaticamente o Bearer Token
 * retornado no cliente, deixando as próximas chamadas autenticadas.
 */
class AuthService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Autentica com email/senha: `POST /auth/login`.
     * Se a conta tiver 2FA, retorna `['requires_2fa' => true, 'challenge' => ...]` —
     * use `send2fa()` + `verify2fa()` para concluir.
     *
     * @param array<string,mixed> $body    `email`, `password`, `turnstile_token`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function login(array $body, array $options = [])
    {
        $response = $this->http->post('/auth/login', $body, $options);
        $this->storeToken($response);

        return $response;
    }

    /**
     * Envia o código 2FA pelo método escolhido: `POST /auth/2fa/send`.
     *
     * @param array<string,mixed> $body    `challenge`, `method` (email|sms|whatsapp|call)
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function send2fa(array $body, array $options = [])
    {
        return $this->http->post('/auth/2fa/send', $body, $options);
    }

    /**
     * Conclui o login com o código 2FA: `POST /auth/login/verify-2fa`.
     *
     * @param array<string,mixed> $body    `challenge`, `code`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function verify2fa(array $body, array $options = [])
    {
        $response = $this->http->post('/auth/login/verify-2fa', $body, $options);
        $this->storeToken($response);

        return $response;
    }

    /**
     * Lista os métodos 2FA ativos da conta: `GET /auth/2fa/methods`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function twoFactorMethods(array $options = [])
    {
        return $this->http->get('/auth/2fa/methods', $options);
    }

    /**
     * Cria uma conta: `POST /auth/register`.
     *
     * @param array<string,mixed> $body    `first_name`, `email`, `cellphone`, `password`, `terms_accepted`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function register(array $body, array $options = [])
    {
        return $this->http->post('/auth/register', $body, $options);
    }

    /**
     * Cadastro simplificado: `POST /auth/register/simple`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function registerSimple(array $body, array $options = [])
    {
        return $this->http->post('/auth/register/simple', $body, $options);
    }

    /**
     * Dispara verificação de email/celular: `POST /auth/verification/send`.
     *
     * @param array<string,mixed> $body    `type` (email|cellphone)
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function verificationSend(array $body, array $options = [])
    {
        return $this->http->post('/auth/verification/send', $body, $options);
    }

    /**
     * Confirma o código de verificação: `POST /auth/verification/verify`.
     *
     * @param array<string,mixed> $body    `code`, `type`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function verificationVerify(array $body, array $options = [])
    {
        return $this->http->post('/auth/verification/verify', $body, $options);
    }

    /**
     * Esqueci a senha: `POST /auth/password/forgot`.
     *
     * @param array<string,mixed> $body    `identifier`, `method` (email|sms|whatsapp)
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function passwordForgot(array $body, array $options = [])
    {
        return $this->http->post('/auth/password/forgot', $body, $options);
    }

    /**
     * Valida o código de recuperação: `POST /auth/password/verify-code`.
     *
     * @param array<string,mixed> $body    `identifier`, `code`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function passwordVerifyCode(array $body, array $options = [])
    {
        return $this->http->post('/auth/password/verify-code', $body, $options);
    }

    /**
     * Redefine a senha: `POST /auth/password/reset`.
     *
     * @param array<string,mixed> $body    `reset_token`, `password`, `password_confirmation`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function passwordReset(array $body, array $options = [])
    {
        return $this->http->post('/auth/password/reset', $body, $options);
    }

    /**
     * Reenvia o código de recuperação: `POST /auth/password/resend`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function passwordResend(array $body, array $options = [])
    {
        return $this->http->post('/auth/password/resend', $body, $options);
    }

    /**
     * Troca a senha logado: `POST /password/change`.
     *
     * @param array<string,mixed> $body    `current_password`, `password`, `password_confirmation`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function changePassword(array $body, array $options = [])
    {
        return $this->http->post('/password/change', $body, $options);
    }

    /**
     * Perfil completo (com estatísticas): `POST /profile`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function profile(array $options = [])
    {
        return $this->http->post('/profile', null, $options);
    }

    /**
     * Perfil atual: `GET /profile/me`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function me(array $options = [])
    {
        return $this->http->get('/profile/me', $options);
    }

    /**
     * Atualiza o perfil: `PUT /profile/me`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function updateMe(array $body, array $options = [])
    {
        return $this->http->put('/profile/me', $body, $options);
    }

    /**
     * Valida o token atual: `GET /auth/verify`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function verify(array $options = [])
    {
        return $this->http->get('/auth/verify', $options);
    }

    /**
     * Renova o JWT: `POST /refresh`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function refresh(array $options = [])
    {
        $response = $this->http->post('/refresh', null, $options);

        $token = null;
        if (is_array($response)) {
            $token = $response['authorization']['token'] ?? $response['token'] ?? null;
        }

        if (is_string($token) && $token !== '') {
            $this->http->setBearerToken($token);
        }

        return $response;
    }

    /**
     * Rotaciona o token: `POST /auth/token/rotate`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function tokenRotate(array $options = [])
    {
        return $this->http->post('/auth/token/rotate', null, $options);
    }

    /**
     * Revoga o token atual: `POST /auth/token/revoke`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function tokenRevoke(array $options = [])
    {
        return $this->http->post('/auth/token/revoke', null, $options);
    }

    /**
     * Encerra a sessão: `POST /auth/logout`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function logout(array $options = [])
    {
        $response = $this->http->post('/auth/logout', null, $options);
        $this->http->setBearerToken(null);

        return $response;
    }

    /**
     * Guarda o Bearer Token quando a resposta trouxer `authorization.token`.
     *
     * @param mixed $response
     */
    private function storeToken($response): void
    {
        if (!is_array($response)) {
            return;
        }

        $token = $response['authorization']['token'] ?? null;
        if (is_string($token) && $token !== '') {
            $this->http->setBearerToken($token);
        }
    }
}
