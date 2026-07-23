<?php

declare(strict_types=1);

/**
 * Gera src/Generated/Catalog.php a partir do catálogo público do gateway
 * APIBrasil (`GET /api/v2/documentations`).
 *
 * Uso:
 *   composer codegen                          # produção (gateway.apibrasil.io)
 *   APIBRASIL_BASE_URL=... composer codegen   # outra base (ex: homolog)
 */

require __DIR__.'/catalog_renderer.php';

$baseUrl = getenv('APIBRASIL_BASE_URL') ?: 'https://gateway.apibrasil.io/api/v2';
$output = dirname(__DIR__).'/src/Generated/Catalog.php';

fwrite(STDOUT, "Baixando catálogo de {$baseUrl}/documentations ...\n");

$payload = fetchCatalog($baseUrl);
$documentations = $payload['documentations'] ?? $payload;

if (!is_array($documentations) || !array_is_list_compat($documentations)) {
    fwrite(STDERR, "Resposta inesperada: \"documentations\" não é uma lista.\n");
    exit(1);
}

/** @var array<string,array<string,bool>> service => set de actions */
$serviceActions = [];
/** @var array<string,array{service:string,fields:string[]}> */
$consultaTipos = [];
/** @var array<string,bool> */
$consultaServicos = [];
$endpointCount = 0;

foreach ($documentations as $doc) {
    foreach ($doc['endpoints'] ?? [] as $endpoint) {
        $url = $endpoint['url'] ?? '';
        if (!is_string($url) || !preg_match('#/api/v2/(.+)$#', $url, $match)) {
            continue;
        }
        ++$endpointCount;

        $fullPath = trim($match[1], '/');
        $segments = explode('/', $fullPath);
        $service = array_shift($segments);
        $action = implode('/', $segments);

        if ($service === '' || $service === null) {
            continue;
        }

        $serviceActions[$service] ??= [];
        if ($action !== '') {
            $serviceActions[$service][$action] = true;
        }

        if (preg_match('#^consulta/([^/]+)/credits$#', $fullPath, $consulta)) {
            $consultaServicos[$consulta[1]] = true;
            $body = $endpoint['body'] ?? null;

            if (is_array($body) && isset($body['tipo']) && is_string($body['tipo']) && $body['tipo'] !== '') {
                $fields = array_values(array_filter(
                    array_keys($body),
                    static fn ($key) => $key !== 'tipo' && $key !== 'homolog'
                ));
                sort($fields, SORT_STRING);
                $consultaTipos[$body['tipo']] = [
                    'service' => $consulta[1],
                    'fields' => array_map('strval', $fields),
                ];
            }
        }
    }
}

$contents = renderCatalog(
    $baseUrl,
    count($documentations),
    $endpointCount,
    array_keys($serviceActions['whatsapp'] ?? []),
    array_keys($serviceActions['evolution'] ?? []),
    array_keys($serviceActions['whatsmeow'] ?? []),
    array_keys($consultaServicos),
    $consultaTipos,
    array_map('array_keys', $serviceActions)
);

if (!is_dir(dirname($output))) {
    mkdir(dirname($output), 0777, true);
}

file_put_contents($output, $contents);

fwrite(STDOUT, sprintf(
    "OK: %s (%d docs, %d endpoints, %d tipos)\n",
    $output,
    count($documentations),
    $endpointCount,
    count($consultaTipos)
));

/**
 * @return array<mixed>
 */
function fetchCatalog(string $baseUrl): array
{
    $url = rtrim($baseUrl, '/').'/documentations';
    $headers = [
        'Accept: application/json',
        'User-Agent: APIBRASIL/SDK-PHP codegen',
    ];

    if (function_exists('curl_init')) {
        $handle = curl_init();
        curl_setopt_array($handle, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 120,
        ]);
        $raw = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($raw === false) {
            fwrite(STDERR, "codegen falhou: {$error}\n");
            exit(1);
        }
    } else {
        $context = stream_context_create([
            'http' => ['header' => implode("\r\n", $headers), 'timeout' => 120],
        ]);
        $raw = @file_get_contents($url, false, $context);
        $status = 200;

        if ($raw === false) {
            fwrite(STDERR, "codegen falhou: não foi possível baixar {$url}\n");
            exit(1);
        }
    }

    if ($status >= 400) {
        fwrite(STDERR, "Falha ao baixar o catálogo: HTTP {$status}\n");
        exit(1);
    }

    $payload = json_decode((string) $raw, true);
    if (!is_array($payload)) {
        fwrite(STDERR, "Falha ao decodificar o catálogo (JSON inválido).\n");
        exit(1);
    }

    return $payload;
}

/**
 * @param array<mixed> $value
 */
function array_is_list_compat(array $value): bool
{
    if (function_exists('array_is_list')) {
        return array_is_list($value);
    }

    return $value === [] || array_keys($value) === range(0, count($value) - 1);
}
