<?php

declare(strict_types=1);

namespace ApiBrasil\Services\Data;

use ApiBrasil\Core\HttpClient;
use ApiBrasil\Services\DeviceProxyService;

/** Tabela FIPE (device-based): `/fipe/{action}` (marcas, modelos, anos, preço...). */
class FipeService extends DeviceProxyService
{
    public function __construct(HttpClient $http)
    {
        parent::__construct($http, 'fipe');
    }
}
