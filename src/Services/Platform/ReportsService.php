<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Platform;

use ApiBrasil\Core\HttpClient;

/**
 * Relatórios e dashboard de consumo (`/reports/*`, `/dashboard/stats`).
 */
class ReportsService
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Estatísticas do dashboard: `GET /dashboard/stats`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function dashboardStats(array $options = [])
    {
        return $this->http->get('/dashboard/stats', $options);
    }

    /**
     * Consumo: `GET /reports/consumption`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function consumption(array $options = [])
    {
        return $this->http->get('/reports/consumption', $options);
    }

    /**
     * Gera relatório de consumo: `POST /reports/generate-consumption-report`.
     *
     * @param array<string,mixed>|null $body
     * @param array<string,mixed>      $options
     *
     * @return mixed
     */
    public function generateConsumptionReport(?array $body = null, array $options = [])
    {
        return $this->http->post('/reports/generate-consumption-report', $body, $options);
    }

    /**
     * Extrato: `GET /reports/extract`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function extract(array $options = [])
    {
        return $this->http->get('/reports/extract', $options);
    }

    /**
     * Dashboard de relatórios: `GET /reports/dashboard`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function dashboard(array $options = [])
    {
        return $this->http->get('/reports/dashboard', $options);
    }

    /**
     * Resumo: `GET /reports/summary`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function summary(array $options = [])
    {
        return $this->http->get('/reports/summary', $options);
    }

    /**
     * Uso diário: `GET /reports/daily-usage`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function dailyUsage(array $options = [])
    {
        return $this->http->get('/reports/daily-usage', $options);
    }

    /**
     * Resumo mensal: `GET /reports/monthly-summary`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function monthlySummary(array $options = [])
    {
        return $this->http->get('/reports/monthly-summary', $options);
    }

    /**
     * Análise de erros: `GET /reports/error-analysis`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function errorAnalysis(array $options = [])
    {
        return $this->http->get('/reports/error-analysis', $options);
    }

    /**
     * Análise por device: `GET /reports/device-analysis`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function deviceAnalysis(array $options = [])
    {
        return $this->http->get('/reports/device-analysis', $options);
    }

    /**
     * Requisições recentes: `GET /reports/recent-requests`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function recentRequests(array $options = [])
    {
        return $this->http->get('/reports/recent-requests', $options);
    }

    /**
     * Estatísticas rápidas: `GET /reports/quick-stats`.
     *
     * @param array<string,mixed> $options
     *
     * @return mixed
     */
    public function quickStats(array $options = [])
    {
        return $this->http->get('/reports/quick-stats', $options);
    }
}
