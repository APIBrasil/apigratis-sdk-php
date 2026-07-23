<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Conta, saldo, faturas, notificações e tickets.
 */
class AccountService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Saldo/créditos da conta: `GET /balance`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function balance(array $options = [])
    {
        return $this->http->get('/balance', $options);
    }

    /**
     * Plano atual: `GET /plan`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function plan(array $options = [])
    {
        return $this->http->get('/plan', $options);
    }

    /**
     * Faturas: `GET /invoices`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function invoices(array $options = [])
    {
        return $this->http->get('/invoices', $options);
    }

    /**
     * Notas fiscais das faturas: `GET /invoices/notes`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function invoiceNotes(array $options = [])
    {
        return $this->http->get('/invoices/notes', $options);
    }

    /**
     * Paga uma fatura: `POST /invoices/pay`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function payInvoice(array $body, array $options = [])
    {
        return $this->http->post('/invoices/pay', $body, $options);
    }

    /**
     * Histórico de requisições da conta: `POST /requests`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function requests(?array $body = null, array $options = [])
    {
        return $this->http->post('/requests', $body, $options);
    }

    /**
     * Histórico de requisições por API: `POST /api/requests`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function apiRequests(?array $body = null, array $options = [])
    {
        return $this->http->post('/api/requests', $body, $options);
    }

    /**
     * Jobs em fila do usuário: `GET /jobs`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function jobs(array $options = [])
    {
        return $this->http->get('/jobs', $options);
    }

    /**
     * Credenciais do usuário: `GET /credentials`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function credentials(array $options = [])
    {
        return $this->http->get('/credentials', $options);
    }

    /**
     * Indicações do usuário: `GET /indications`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function indications(array $options = [])
    {
        return $this->http->get('/indications', $options);
    }

    /**
     * Notificações: `GET /notifications`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function notifications(array $options = [])
    {
        return $this->http->get('/notifications', $options);
    }

    /**
     * Marca uma notificação como lida: `PATCH /notifications/{id}/read`.
     *
     * @param string|int          $id
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function markNotificationRead($id, array $options = [])
    {
        return $this->http->patch("/notifications/{$id}/read", null, $options);
    }

    /**
     * Marca todas as notificações como lidas: `POST /notifications/mark-all-read`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function markAllNotificationsRead(array $options = [])
    {
        return $this->http->post('/notifications/mark-all-read', null, $options);
    }

    /**
     * Tickets de suporte: `GET /tickets`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function tickets(array $options = [])
    {
        return $this->http->get('/tickets', $options);
    }

    /**
     * Abre um ticket: `POST /ticket`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function createTicket(array $body, array $options = [])
    {
        return $this->http->post('/ticket', $body, $options);
    }

    /**
     * Atualiza um ticket: `PUT /ticket/{id}`.
     *
     * @param string|int          $id
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function updateTicket($id, array $body, array $options = [])
    {
        return $this->http->put("/ticket/{$id}", $body, $options);
    }

    /**
     * Mensagens de um ticket: `GET /ticket/{id}/messages`.
     *
     * @param string|int          $id
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function ticketMessages($id, array $options = [])
    {
        return $this->http->get("/ticket/{$id}/messages", $options);
    }

    /**
     * Responde um ticket: `POST /ticket/{id}/messages`.
     *
     * @param string|int          $id
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function addTicketMessage($id, array $body, array $options = [])
    {
        return $this->http->post("/ticket/{$id}/messages", $body, $options);
    }
}
