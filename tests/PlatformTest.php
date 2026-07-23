<?php

declare(strict_types=1);

namespace ApiBrasil\Test;

use ApiBrasil\ApiBrasil;
use ApiBrasil\Services\Platform\PaymentsService;
use ApiBrasil\Test\Helpers\ApiTestCase;
use ApiBrasil\Test\Helpers\FakeTransport;

class PlatformTest extends ApiTestCase
{
    /**
     * @dataProvider authCases
     * @dataProvider devicesCases
     * @dataProvider accountCases
     * @dataProvider paymentsCases
     * @dataProvider catalogCases
     * @dataProvider securityCases
     * @dataProvider reportsCases
     *
     * @param callable(ApiBrasil):mixed $call
     * @param array<string,mixed>|null  $body
     */
    public function test_rota(callable $call, string $path, string $method = 'POST', ?array $body = null): void
    {
        $this->assertRoute($call, $path, $method, $body);
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function authCases(): array
    {
        return [
            'auth.login → POST /auth/login' => [
                static fn (ApiBrasil $api) => $api->auth->login(['email' => 'a@b.c', 'password' => 'x']),
                '/auth/login',
                'POST',
                ['email' => 'a@b.c', 'password' => 'x'],
            ],
            'auth.send2fa → POST /auth/2fa/send' => [
                static fn (ApiBrasil $api) => $api->auth->send2fa(['challenge' => 'ch', 'method' => 'email']),
                '/auth/2fa/send',
                'POST',
                ['challenge' => 'ch', 'method' => 'email'],
            ],
            'auth.verify2fa → POST /auth/login/verify-2fa' => [
                static fn (ApiBrasil $api) => $api->auth->verify2fa(['challenge' => 'ch', 'code' => '000000']),
                '/auth/login/verify-2fa',
                'POST',
                null,
            ],
            'auth.twoFactorMethods → GET /auth/2fa/methods' => [
                static fn (ApiBrasil $api) => $api->auth->twoFactorMethods(),
                '/auth/2fa/methods',
                'GET',
                null,
            ],
            'auth.register → POST /auth/register' => [
                static fn (ApiBrasil $api) => $api->auth->register([
                    'first_name' => 'Fulano',
                    'email' => 'a@b.c',
                    'cellphone' => '11999999999',
                    'password' => 'x',
                    'terms_accepted' => true,
                ]),
                '/auth/register',
                'POST',
                null,
            ],
            'auth.registerSimple → POST /auth/register/simple' => [
                static fn (ApiBrasil $api) => $api->auth->registerSimple([
                    'email' => 'a@b.c',
                    'password' => 'x',
                    'password_confirmation' => 'x',
                ]),
                '/auth/register/simple',
                'POST',
                null,
            ],
            'auth.verificationSend → POST /auth/verification/send' => [
                static fn (ApiBrasil $api) => $api->auth->verificationSend(['type' => 'email']),
                '/auth/verification/send',
                'POST',
                ['type' => 'email'],
            ],
            'auth.verificationVerify → POST /auth/verification/verify' => [
                static fn (ApiBrasil $api) => $api->auth->verificationVerify(['code' => '123456', 'type' => 'email']),
                '/auth/verification/verify',
                'POST',
                null,
            ],
            'auth.passwordForgot → POST /auth/password/forgot' => [
                static fn (ApiBrasil $api) => $api->auth->passwordForgot(['identifier' => 'a@b.c', 'method' => 'email']),
                '/auth/password/forgot',
                'POST',
                null,
            ],
            'auth.passwordVerifyCode → POST /auth/password/verify-code' => [
                static fn (ApiBrasil $api) => $api->auth->passwordVerifyCode(['identifier' => 'a@b.c', 'code' => '123456']),
                '/auth/password/verify-code',
                'POST',
                null,
            ],
            'auth.passwordReset → POST /auth/password/reset' => [
                static fn (ApiBrasil $api) => $api->auth->passwordReset([
                    'reset_token' => 't',
                    'password' => 'x',
                    'password_confirmation' => 'x',
                ]),
                '/auth/password/reset',
                'POST',
                null,
            ],
            'auth.passwordResend → POST /auth/password/resend' => [
                static fn (ApiBrasil $api) => $api->auth->passwordResend(['identifier' => 'a@b.c', 'method' => 'sms']),
                '/auth/password/resend',
                'POST',
                null,
            ],
            'auth.changePassword → POST /password/change' => [
                static fn (ApiBrasil $api) => $api->auth->changePassword([
                    'current_password' => 'a',
                    'password' => 'b',
                    'password_confirmation' => 'b',
                ]),
                '/password/change',
                'POST',
                null,
            ],
            'auth.profile → POST /profile' => [
                static fn (ApiBrasil $api) => $api->auth->profile(),
                '/profile',
                'POST',
                null,
            ],
            'auth.me → GET /profile/me' => [
                static fn (ApiBrasil $api) => $api->auth->me(),
                '/profile/me',
                'GET',
                null,
            ],
            'auth.updateMe → PUT /profile/me' => [
                static fn (ApiBrasil $api) => $api->auth->updateMe(['first_name' => 'Novo']),
                '/profile/me',
                'PUT',
                ['first_name' => 'Novo'],
            ],
            'auth.verify → GET /auth/verify' => [
                static fn (ApiBrasil $api) => $api->auth->verify(),
                '/auth/verify',
                'GET',
                null,
            ],
            'auth.refresh → POST /refresh' => [
                static fn (ApiBrasil $api) => $api->auth->refresh(),
                '/refresh',
                'POST',
                null,
            ],
            'auth.tokenRotate → POST /auth/token/rotate' => [
                static fn (ApiBrasil $api) => $api->auth->tokenRotate(),
                '/auth/token/rotate',
                'POST',
                null,
            ],
            'auth.tokenRevoke → POST /auth/token/revoke' => [
                static fn (ApiBrasil $api) => $api->auth->tokenRevoke(),
                '/auth/token/revoke',
                'POST',
                null,
            ],
            'auth.logout → POST /auth/logout' => [
                static fn (ApiBrasil $api) => $api->auth->logout(),
                '/auth/logout',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function devicesCases(): array
    {
        return [
            'devices.list → GET /devices' => [
                static fn (ApiBrasil $api) => $api->devices->list(),
                '/devices',
                'GET',
                null,
            ],
            'devices.list paginado → GET /devices?paginate=true' => [
                static fn (ApiBrasil $api) => $api->devices->list(['paginate' => true]),
                '/devices?paginate=true',
                'GET',
                null,
            ],
            'devices.store → POST /devices/store' => [
                static fn (ApiBrasil $api) => $api->devices->store(['device_name' => 'bot', 'type' => 'server']),
                '/devices/store',
                'POST',
                ['device_name' => 'bot', 'type' => 'server'],
            ],
            'devices.show → GET /devices/show?search={device}' => [
                static fn (ApiBrasil $api) => $api->devices->show(),
                '/devices/show?search=dev',
                'GET',
                null,
            ],
            'devices.show explícito → GET /devices/show?search=outro' => [
                static fn (ApiBrasil $api) => $api->devices->show('outro'),
                '/devices/show?search=outro',
                'GET',
                null,
            ],
            'devices.update → POST /devices/update' => [
                static fn (ApiBrasil $api) => $api->devices->update(['device_token' => 'dev', 'device_name' => 'novo']),
                '/devices/update',
                'POST',
                null,
            ],
            'devices.destroy → DELETE /devices/destroy' => [
                static fn (ApiBrasil $api) => $api->devices->destroy(),
                '/devices/destroy',
                'DELETE',
                ['search' => 'dev'],
            ],
            'devices.requests → POST /devices/requests' => [
                static fn (ApiBrasil $api) => $api->devices->requests(['page' => 1]),
                '/devices/requests',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function accountCases(): array
    {
        return [
            'account.balance → GET /balance' => [
                static fn (ApiBrasil $api) => $api->account->balance(),
                '/balance',
                'GET',
                null,
            ],
            'account.plan → GET /plan' => [
                static fn (ApiBrasil $api) => $api->account->plan(),
                '/plan',
                'GET',
                null,
            ],
            'account.invoices → GET /invoices' => [
                static fn (ApiBrasil $api) => $api->account->invoices(),
                '/invoices',
                'GET',
                null,
            ],
            'account.invoiceNotes → GET /invoices/notes' => [
                static fn (ApiBrasil $api) => $api->account->invoiceNotes(),
                '/invoices/notes',
                'GET',
                null,
            ],
            'account.payInvoice → POST /invoices/pay' => [
                static fn (ApiBrasil $api) => $api->account->payInvoice(['invoice' => 9]),
                '/invoices/pay',
                'POST',
                ['invoice' => 9],
            ],
            'account.requests → POST /requests' => [
                static fn (ApiBrasil $api) => $api->account->requests(['page' => 1]),
                '/requests',
                'POST',
                null,
            ],
            'account.apiRequests → POST /api/requests' => [
                static fn (ApiBrasil $api) => $api->account->apiRequests(),
                '/api/requests',
                'POST',
                null,
            ],
            'account.jobs → GET /jobs' => [
                static fn (ApiBrasil $api) => $api->account->jobs(),
                '/jobs',
                'GET',
                null,
            ],
            'account.credentials → GET /credentials' => [
                static fn (ApiBrasil $api) => $api->account->credentials(),
                '/credentials',
                'GET',
                null,
            ],
            'account.indications → GET /indications' => [
                static fn (ApiBrasil $api) => $api->account->indications(),
                '/indications',
                'GET',
                null,
            ],
            'account.notifications → GET /notifications' => [
                static fn (ApiBrasil $api) => $api->account->notifications(),
                '/notifications',
                'GET',
                null,
            ],
            'account.markNotificationRead → PATCH /notifications/{id}/read' => [
                static fn (ApiBrasil $api) => $api->account->markNotificationRead(9),
                '/notifications/9/read',
                'PATCH',
                null,
            ],
            'account.markAllNotificationsRead → POST /notifications/mark-all-read' => [
                static fn (ApiBrasil $api) => $api->account->markAllNotificationsRead(),
                '/notifications/mark-all-read',
                'POST',
                null,
            ],
            'account.tickets → GET /tickets' => [
                static fn (ApiBrasil $api) => $api->account->tickets(),
                '/tickets',
                'GET',
                null,
            ],
            'account.createTicket → POST /ticket' => [
                static fn (ApiBrasil $api) => $api->account->createTicket(['subject' => 'Ajuda']),
                '/ticket',
                'POST',
                null,
            ],
            'account.updateTicket → PUT /ticket/{id}' => [
                static fn (ApiBrasil $api) => $api->account->updateTicket(7, ['status' => 'closed']),
                '/ticket/7',
                'PUT',
                null,
            ],
            'account.ticketMessages → GET /ticket/{id}/messages' => [
                static fn (ApiBrasil $api) => $api->account->ticketMessages(7),
                '/ticket/7/messages',
                'GET',
                null,
            ],
            'account.addTicketMessage → POST /ticket/{id}/messages' => [
                static fn (ApiBrasil $api) => $api->account->addTicketMessage(7, ['message' => 'oi']),
                '/ticket/7/messages',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function paymentsCases(): array
    {
        return [
            'payments.recharges → GET /recharges' => [
                static fn (ApiBrasil $api) => $api->payments->recharges(),
                '/recharges',
                'GET',
                null,
            ],
            'payments.recharge → POST /recharge' => [
                static fn (ApiBrasil $api) => $api->payments->recharge(['amount' => 100, 'type' => 'pix']),
                '/recharge',
                'POST',
                ['amount' => 100, 'type' => 'pix'],
            ],
            'payments.rechargeShow → GET /recharge/{id}' => [
                static fn (ApiBrasil $api) => $api->payments->rechargeShow('rec-1'),
                '/recharge/rec-1',
                'GET',
                null,
            ],
            'payments.cardProcess → POST /mercadopago/card/process' => [
                static fn (ApiBrasil $api) => $api->payments->cardProcess(['token' => 'tok']),
                '/mercadopago/card/process',
                'POST',
                null,
            ],
            'payments.cardInstallments → POST /mercadopago/card/installments' => [
                static fn (ApiBrasil $api) => $api->payments->cardInstallments(['amount' => 100]),
                '/mercadopago/card/installments',
                'POST',
                null,
            ],
            'payments.cardStatus → GET /mercadopago/card/{id}' => [
                static fn (ApiBrasil $api) => $api->payments->cardStatus('pay-1'),
                '/mercadopago/card/pay-1',
                'GET',
                null,
            ],
            'payments.checkoutPaymentMethods → GET /checkout/payment-methods' => [
                static fn (ApiBrasil $api) => $api->payments->checkoutPaymentMethods(),
                '/checkout/payment-methods',
                'GET',
                null,
            ],
            'payments.checkoutPeriods → GET /checkout/periods' => [
                static fn (ApiBrasil $api) => $api->payments->checkoutPeriods(),
                '/checkout/periods',
                'GET',
                null,
            ],
            'payments.validateCoupon → POST /checkout/validate-coupon' => [
                static fn (ApiBrasil $api) => $api->payments->validateCoupon(['coupon' => 'DEV10']),
                '/checkout/validate-coupon',
                'POST',
                null,
            ],
            'payments.checkoutFinalize → POST /checkout/finalize' => [
                static fn (ApiBrasil $api) => $api->payments->checkoutFinalize(['plan' => 1]),
                '/checkout/finalize',
                'POST',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function catalogCases(): array
    {
        return [
            'catalog.apis → GET /apis' => [
                static fn (ApiBrasil $api) => $api->catalog->apis(),
                '/apis',
                'GET',
                null,
            ],
            'catalog.apis com busca → GET /apis?search=x' => [
                static fn (ApiBrasil $api) => $api->catalog->apis('whatsapp'),
                '/apis?search=whatsapp',
                'GET',
                null,
            ],
            'catalog.api → GET /apis/{identifier}' => [
                static fn (ApiBrasil $api) => $api->catalog->api('42'),
                '/apis/42',
                'GET',
                null,
            ],
            'catalog.apiByName → GET /apis/name/{name}' => [
                static fn (ApiBrasil $api) => $api->catalog->apiByName('whatsapp-wpp'),
                '/apis/name/whatsapp-wpp',
                'GET',
                null,
            ],
            'catalog.apiCategories → GET /apis/categories' => [
                static fn (ApiBrasil $api) => $api->catalog->apiCategories(),
                '/apis/categories',
                'GET',
                null,
            ],
            'catalog.myApis → GET /apis/list' => [
                static fn (ApiBrasil $api) => $api->catalog->myApis(),
                '/apis/list',
                'GET',
                null,
            ],
            'catalog.apisByDevice → GET /apis/device/{token}' => [
                static fn (ApiBrasil $api) => $api->catalog->apisByDevice('dev-1'),
                '/apis/device/dev-1',
                'GET',
                null,
            ],
            'catalog.plans → GET /plans' => [
                static fn (ApiBrasil $api) => $api->catalog->plans(),
                '/plans',
                'GET',
                null,
            ],
            'catalog.documentations → GET /documentations' => [
                static fn (ApiBrasil $api) => $api->catalog->documentations(),
                '/documentations',
                'GET',
                null,
            ],
            'catalog.documentationsByServer → GET /documentations/server/{s}' => [
                static fn (ApiBrasil $api) => $api->catalog->documentationsByServer('srv-1'),
                '/documentations/server/srv-1',
                'GET',
                null,
            ],
            'catalog.servers → GET /servers' => [
                static fn (ApiBrasil $api) => $api->catalog->servers(),
                '/servers',
                'GET',
                null,
            ],
            'catalog.endpointUrl → POST /endpoint/url' => [
                static fn (ApiBrasil $api) => $api->catalog->endpointUrl(['action' => 'sendText']),
                '/endpoint/url',
                'POST',
                null,
            ],
            'catalog.endpointBody → POST /endpoint/body' => [
                static fn (ApiBrasil $api) => $api->catalog->endpointBody(['action' => 'sendText']),
                '/endpoint/body',
                'POST',
                null,
            ],
            'catalog.status → GET /status' => [
                static fn (ApiBrasil $api) => $api->catalog->status(),
                '/status',
                'GET',
                null,
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function securityCases(): array
    {
        return [
            'ipWhitelist.get → GET /ip-whitelist' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->get(),
                '/ip-whitelist',
                'GET',
                null,
            ],
            'ipWhitelist.set → PUT /ip-whitelist' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->set(['1.2.3.4']),
                '/ip-whitelist',
                'PUT',
                ['ip_whitelist' => ['1.2.3.4']],
            ],
            'ipWhitelist.add → POST /ip-whitelist/add' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->add('1.2.3.4'),
                '/ip-whitelist/add',
                'POST',
                ['entry' => '1.2.3.4'],
            ],
            'ipWhitelist.remove → DELETE /ip-whitelist/remove' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->remove('1.2.3.4'),
                '/ip-whitelist/remove',
                'DELETE',
                ['entry' => '1.2.3.4'],
            ],
            'ipWhitelist.addCurrent → POST /ip-whitelist/add-current' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->addCurrent(),
                '/ip-whitelist/add-current',
                'POST',
                null,
            ],
            'ipWhitelist.reset → POST /ip-whitelist/reset' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->reset(),
                '/ip-whitelist/reset',
                'POST',
                null,
            ],
            'ipWhitelist.validate → POST /ip-whitelist/validate' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->validate('10.0.0.0/8'),
                '/ip-whitelist/validate',
                'POST',
                ['entry' => '10.0.0.0/8'],
            ],
            'ipWhitelist.currentIp → GET /ip-whitelist/current-ip' => [
                static fn (ApiBrasil $api) => $api->ipWhitelist->currentIp(),
                '/ip-whitelist/current-ip',
                'GET',
                null,
            ],
            'bearerRateLimit.get → GET /bearer-rate-limit' => [
                static fn (ApiBrasil $api) => $api->bearerRateLimit->get(),
                '/bearer-rate-limit',
                'GET',
                null,
            ],
            'bearerRateLimit.set → PUT /bearer-rate-limit' => [
                static fn (ApiBrasil $api) => $api->bearerRateLimit->set(['limit' => 120]),
                '/bearer-rate-limit',
                'PUT',
                ['limit' => 120],
            ],
        ];
    }

    /** @return array<string,array{0:callable,1:string,2:string,3:array<string,mixed>|null}> */
    public function reportsCases(): array
    {
        return [
            'reports.dashboardStats → GET /dashboard/stats' => [
                static fn (ApiBrasil $api) => $api->reports->dashboardStats(),
                '/dashboard/stats',
                'GET',
                null,
            ],
            'reports.consumption → GET /reports/consumption' => [
                static fn (ApiBrasil $api) => $api->reports->consumption(),
                '/reports/consumption',
                'GET',
                null,
            ],
            'reports.generateConsumptionReport → POST /reports/generate-consumption-report' => [
                static fn (ApiBrasil $api) => $api->reports->generateConsumptionReport(['month' => '2026-07']),
                '/reports/generate-consumption-report',
                'POST',
                null,
            ],
            'reports.extract → GET /reports/extract' => [
                static fn (ApiBrasil $api) => $api->reports->extract(),
                '/reports/extract',
                'GET',
                null,
            ],
            'reports.dashboard → GET /reports/dashboard' => [
                static fn (ApiBrasil $api) => $api->reports->dashboard(),
                '/reports/dashboard',
                'GET',
                null,
            ],
            'reports.summary → GET /reports/summary' => [
                static fn (ApiBrasil $api) => $api->reports->summary(),
                '/reports/summary',
                'GET',
                null,
            ],
            'reports.dailyUsage → GET /reports/daily-usage' => [
                static fn (ApiBrasil $api) => $api->reports->dailyUsage(),
                '/reports/daily-usage',
                'GET',
                null,
            ],
            'reports.monthlySummary → GET /reports/monthly-summary' => [
                static fn (ApiBrasil $api) => $api->reports->monthlySummary(),
                '/reports/monthly-summary',
                'GET',
                null,
            ],
            'reports.errorAnalysis → GET /reports/error-analysis' => [
                static fn (ApiBrasil $api) => $api->reports->errorAnalysis(),
                '/reports/error-analysis',
                'GET',
                null,
            ],
            'reports.deviceAnalysis → GET /reports/device-analysis' => [
                static fn (ApiBrasil $api) => $api->reports->deviceAnalysis(),
                '/reports/device-analysis',
                'GET',
                null,
            ],
            'reports.recentRequests → GET /reports/recent-requests' => [
                static fn (ApiBrasil $api) => $api->reports->recentRequests(),
                '/reports/recent-requests',
                'GET',
                null,
            ],
            'reports.quickStats → GET /reports/quick-stats' => [
                static fn (ApiBrasil $api) => $api->reports->quickStats(),
                '/reports/quick-stats',
                'GET',
                null,
            ],
        ];
    }

    public function test_refresh_guarda_o_novo_token_retornado(): void
    {
        [$transport, $api] = $this->buildApi();
        $transport->respondWith(FakeTransport::ok(['authorization' => ['token' => 'jwt-refresh']]));

        $api->auth->refresh();
        $api->account->balance();

        $this->assertSame('Bearer jwt-refresh', $transport->lastHeaders()['Authorization']);
    }

    public function test_logout_limpa_o_bearer_token_do_cliente(): void
    {
        [$transport, $api] = $this->buildApi();
        $api->auth->logout();
        $api->catalog->plans();

        $this->assertArrayNotHasKey('Authorization', $transport->lastHeaders());
    }

    public function test_devices_store_envia_o_header_secretkey(): void
    {
        [$explicitTransport, $explicitApi] = $this->buildApi();
        $explicitApi->devices->store(['device_name' => 'bot'], ['secretKey' => 'sk-1']);
        $this->assertSame('sk-1', $explicitTransport->lastHeaders()['SecretKey']);

        [$configTransport, $configApi] = $this->buildApi(['secretKey' => 'sk-config']);
        $configApi->devices->store(['device_name' => 'bot']);
        $this->assertSame('sk-config', $configTransport->lastHeaders()['SecretKey']);
    }

    /**
     * @dataProvider provedoresDePagamento
     */
    public function test_pagamentos_por_provedor(string $provider): void
    {
        [$transport, $api] = $this->buildApi();

        $api->payments->pixGenerate($provider, ['amount' => 100]);
        $this->assertSame(self::BASE."/{$provider}/pix/generate", $transport->last()->url);
        $this->assertEquals(['amount' => 100], $transport->lastBody());

        $api->payments->pixStatus($provider, 'tx-1');
        $this->assertSame('GET', $transport->last()->method);
        $this->assertSame(self::BASE."/{$provider}/pix/tx-1", $transport->last()->url);

        $api->payments->boletoGenerate($provider, ['amount' => 150]);
        $this->assertSame(self::BASE."/{$provider}/boleto/generate", $transport->last()->url);

        $api->payments->boletoStatus($provider, 'b-1');
        $this->assertSame('GET', $transport->last()->method);
        $this->assertSame(self::BASE."/{$provider}/boleto/b-1", $transport->last()->url);

        $api->payments->boletoPdf($provider, 'b-1');
        $this->assertSame(self::BASE."/{$provider}/boleto/b-1/pdf", $transport->last()->url);
        $this->assertSame('raw', $transport->last()->responseType);
    }

    /** @return array<string,array{0:string}> */
    public function provedoresDePagamento(): array
    {
        $cases = [];
        foreach (PaymentsService::PROVIDERS as $provider) {
            $cases[$provider] = [$provider];
        }

        return $cases;
    }
}
