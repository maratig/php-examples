<?php

namespace ApiClient\KoronaAuto;

use ApiClient\KoronaAuto\Config\Config as KoronaConfig;
use ApiClient\KoronaAuto\Config\Factory\ConfigFactory as KoronaConfigFactory;
use ApiClient\KoronaAuto\Filter\Factory\SuitableProductsFactory;
use ApiClient\KoronaAuto\Filter\SuitableProducts;
use ApiClient\KoronaAuto\Hydrator\Factory\KoronaInfoHydratorFactory;
use ApiClient\KoronaAuto\Hydrator\Factory\ProductTOAggregateHydratorFactory;
use ApiClient\KoronaAuto\Service\AssembleService;
use ApiClient\KoronaAuto\Service\Factory\AssembleServiceFactory;

return [
    'service_manager' => [
        'factories' => [
            KoronaConfig::class    => KoronaConfigFactory::class,
            AssembleService::class => AssembleServiceFactory::class,
        ],
    ],
    'api-client'      => [
        'korona-auto' => [
            'baseUrl'         => 'https://korona-auto.com/api',
            'apiUid'          => '',
            'cache' => [
                'storage' => 'MemcachedStorage',
            ],
            'alias'           => 'koronaAuto',
            'sourceName'      => 'korona-auto',
            'assembleService' => AssembleService::class,
            'threadsNum'      => 10,
        ],
    ],
    'hydrators'       => [
        'factories' => [
            'ProdTOHydrator'     => ProductTOAggregateHydratorFactory::class,
            'KoronaInfoHydrator' => KoronaInfoHydratorFactory::class,
        ],
    ],
    'filters'         => [
        'factories' => [
            SuitableProducts::class => SuitableProductsFactory::class,
        ],
    ],
];
