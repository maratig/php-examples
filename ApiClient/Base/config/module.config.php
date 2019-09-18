<?php

namespace ApiClient\Base;

use ApiClient\Base\Service\ApiClientInterface;
use ApiClient\Base\Service\BaseClient;
use ApiClient\Base\Service\Factory\BaseClientFactory;

return [
    'service_manager' => [
        'aliases'   => [
            ApiClientInterface::class => BaseClient::class,
        ],
        'factories' => [
            BaseClient::class => BaseClientFactory::class,
        ],
    ],
    'api-client'      => [
        'base' => [
            'cache' => [
                'storage' => 'MemcachedStorage',
                'options' => [
                    'ttl' => 1800,
                ],
            ],
        ],
    ],
];