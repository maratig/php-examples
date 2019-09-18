<?php

namespace ApiClient\KoronaAuto\Hydrator\Factory;

use ApiClient\KoronaAuto\Hydrator\ManufacturerTOHydrator;
use ApiClient\KoronaAuto\Hydrator\ProductTOHydrator;
use ApiClient\KoronaAuto\Hydrator\ProductWarehouseTOHydrator;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\Aggregate\AggregateHydrator;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ProductTOAggregateHydratorFactory
 *
 * @package ApiClient\KoronaAuto\Hydrator\Factory
 */
class ProductTOAggregateHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AggregateHydrator
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = new AggregateHydrator();
        $hydrator->add(new ProductTOHydrator());
        $hydrator->add(new ManufacturerTOHydrator());
        $hydrator->add(new ProductWarehouseTOHydrator());

        return $hydrator;
    }
}