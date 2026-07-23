<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Pagamentos e recargas (PIX, boleto e cartão) — `/recharge`,
 * `/{provider}/pix/*`, `/{provider}/boleto/*`, `/mercadopago/card/*`.
 */
class PaymentsService
{
    /** Provedores de pagamento suportados pelo gateway. */
    public const PROVIDERS = ['santander', 'inter', 'mercadopago', 'sicoob'];

    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Lista as recargas: `GET /recharges`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function recharges(array $options = [])
    {
        return $this->http->get('/recharges', $options);
    }

    /**
     * Cria uma recarga (pix|boleto): `POST /recharge`.
     *
     * @param array<string,mixed> $body    `amount`, `type`
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function recharge(array $body, array $options = [])
    {
        return $this->http->post('/recharge', $body, $options);
    }

    /**
     * Detalha uma recarga: `GET /recharge/{identifier}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function rechargeShow(string $identifier, array $options = [])
    {
        return $this->http->get('/recharge/'.$identifier, $options);
    }

    /**
     * Gera uma cobrança PIX: `POST /{provider}/pix/generate`.
     *
     * @param string              $provider um de `self::PROVIDERS`
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function pixGenerate(string $provider, array $body, array $options = [])
    {
        return $this->http->post("/{$provider}/pix/generate", $body, $options);
    }

    /**
     * Consulta uma cobrança PIX: `GET /{provider}/pix/{txId}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function pixStatus(string $provider, string $txId, array $options = [])
    {
        return $this->http->get("/{$provider}/pix/{$txId}", $options);
    }

    /**
     * Gera um boleto: `POST /{provider}/boleto/generate`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function boletoGenerate(string $provider, array $body, array $options = [])
    {
        return $this->http->post("/{$provider}/boleto/generate", $body, $options);
    }

    /**
     * Consulta um boleto: `GET /{provider}/boleto/{id}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function boletoStatus(string $provider, string $id, array $options = [])
    {
        return $this->http->get("/{$provider}/boleto/{$id}", $options);
    }

    /**
     * Baixa o PDF de um boleto: `GET /{provider}/boleto/{id}/pdf`.
     * Devolve o conteúdo binário do arquivo.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function boletoPdf(string $provider, string $id, array $options = [])
    {
        return $this->http->get(
            "/{$provider}/boleto/{$id}/pdf",
            array_merge(['responseType' => 'raw'], $options)
        );
    }

    /**
     * Processa pagamento com cartão (Mercado Pago): `POST /mercadopago/card/process`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cardProcess(array $body, array $options = [])
    {
        return $this->http->post('/mercadopago/card/process', $body, $options);
    }

    /**
     * Consulta parcelas do cartão: `POST /mercadopago/card/installments`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cardInstallments(array $body, array $options = [])
    {
        return $this->http->post('/mercadopago/card/installments', $body, $options);
    }

    /**
     * Consulta um pagamento de cartão: `GET /mercadopago/card/{id}`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function cardStatus(string $id, array $options = [])
    {
        return $this->http->get('/mercadopago/card/'.$id, $options);
    }

    /**
     * Métodos de pagamento do checkout: `GET /checkout/payment-methods`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function checkoutPaymentMethods(array $options = [])
    {
        return $this->http->get('/checkout/payment-methods', $options);
    }

    /**
     * Períodos do checkout: `GET /checkout/periods`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function checkoutPeriods(array $options = [])
    {
        return $this->http->get('/checkout/periods', $options);
    }

    /**
     * Valida um cupom: `POST /checkout/validate-coupon`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function validateCoupon(array $body, array $options = [])
    {
        return $this->http->post('/checkout/validate-coupon', $body, $options);
    }

    /**
     * Finaliza o checkout: `POST /checkout/finalize`.
     *
     * @param array<string,mixed> $body
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function checkoutFinalize(array $body, array $options = [])
    {
        return $this->http->post('/checkout/finalize', $body, $options);
    }
}
