<?php

declare(strict_types=1);

/**
 * Renderizador do arquivo src/Generated/Catalog.php.
 *
 * Compartilhado por `scripts/codegen.php` (baixa o catálogo do gateway) —
 * mantém o formato do arquivo gerado em um só lugar.
 */

if (!function_exists('catalogQuote')) {
    function catalogQuote(string $value): string
    {
        return "'".str_replace(["\\", "'"], ["\\\\", "\\'"], $value)."'";
    }
}

if (!function_exists('catalogList')) {
    /**
     * @param string[] $values
     */
    function catalogList(array $values, string $indent = '        '): string
    {
        if ($values === []) {
            return '';
        }

        sort($values, SORT_STRING);

        return implode("\n", array_map(
            static fn (string $value): string => $indent.catalogQuote($value).',',
            $values
        ))."\n";
    }
}

if (!function_exists('renderCatalog')) {
    /**
     * @param string[]                                     $whatsappActions
     * @param string[]                                     $evolutionPaths
     * @param string[]                                     $whatsmeowActions
     * @param string[]                                     $consultaServicos
     * @param array<string,array{service:string,fields:string[]}> $consultaTipos
     * @param array<string,string[]>                       $serviceActions
     */
    function renderCatalog(
        string $baseUrl,
        int $docCount,
        int $endpointCount,
        array $whatsappActions,
        array $evolutionPaths,
        array $whatsmeowActions,
        array $consultaServicos,
        array $consultaTipos,
        array $serviceActions
    ): string {
        ksort($consultaTipos, SORT_STRING);
        ksort($serviceActions, SORT_STRING);

        $tiposLiteral = '';
        foreach ($consultaTipos as $tipo => $meta) {
            $fields = $meta['fields'];
            sort($fields, SORT_STRING);
            $fieldsLiteral = implode(', ', array_map('catalogQuote', $fields));
            $tiposLiteral .= sprintf(
                "        %s => ['service' => %s, 'fields' => [%s]],\n",
                catalogQuote((string) $tipo),
                catalogQuote($meta['service']),
                $fieldsLiteral
            );
        }

        $actionsLiteral = '';
        foreach ($serviceActions as $service => $actions) {
            sort($actions, SORT_STRING);
            $actionsLiteral .= sprintf(
                "        %s => [%s],\n",
                catalogQuote((string) $service),
                implode(', ', array_map('catalogQuote', $actions))
            );
        }

        $whatsapp = catalogList($whatsappActions);
        $evolution = catalogList($evolutionPaths);
        $whatsmeow = catalogList($whatsmeowActions);
        $servicos = catalogList($consultaServicos);
        $tipoCount = count($consultaTipos);

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace ApiBrasil\\Generated;

        /**
         * ARQUIVO GERADO AUTOMATICAMENTE — não edite manualmente.
         *
         * Fonte: {$baseUrl}/documentations
         * Regenerar: composer codegen
         *
         * {$docCount} documentações, {$endpointCount} endpoints,
         * {$tipoCount} tipos de consulta conhecidos.
         */
        final class Catalog
        {
            /**
             * Actions conhecidas da API de WhatsApp (POST /whatsapp/{action}).
             *
             * @var string[]
             */
            public const WHATSAPP_ACTIONS = [
        {$whatsapp}    ];

            /**
             * Caminhos conhecidos da Evolution API (POST /evolution/{controller}/{action}).
             *
             * @var string[]
             */
            public const EVOLUTION_PATHS = [
        {$evolution}    ];

            /**
             * Actions conhecidas do WhatsMeow (POST /whatsmeow/{action}).
             *
             * @var string[]
             */
            public const WHATSMEOW_ACTIONS = [
        {$whatsmeow}    ];

            /**
             * Serviços de consulta por crédito (POST /consulta/{service}/credits).
             *
             * @var string[]
             */
            public const CONSULTA_SERVICOS = [
        {$servicos}    ];

            /**
             * Metadados por tipo de consulta: serviço da rota e campos do body de exemplo.
             *
             * @var array<string,array{service:string,fields:string[]}>
             */
            public const CONSULTA_TIPOS = [
        {$tiposLiteral}    ];

            /**
             * Actions documentadas por serviço do gateway.
             *
             * @var array<string,string[]>
             */
            public const SERVICE_ACTIONS = [
        {$actionsLiteral}    ];

            /**
             * Metadados de um tipo de consulta (`null` quando desconhecido).
             *
             * @return array{service:string,fields:string[]}|null
             */
            public static function consultaTipo(string \$tipo): ?array
            {
                return self::CONSULTA_TIPOS[\$tipo] ?? null;
            }

            /**
             * Actions documentadas de um serviço.
             *
             * @return string[]
             */
            public static function serviceActions(string \$service): array
            {
                return self::SERVICE_ACTIONS[\$service] ?? [];
            }
        }

        PHP;
    }
}
