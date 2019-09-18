<?php

namespace ApiClient\KoronaAuto\Hydrator\Factory;

use ApiClient\KoronaAuto\Hydrator\Strategy\KoronaPricesStrategy;
use ApiClient\KoronaAuto\Hydrator\Strategy\KoronaStockStrategy;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ObjectProperty;
use Zend\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class KoronaInfoHydratorFactory
 *
 * @package ApiClient\KoronaAuto\Hydrator\Factory
 */
class KoronaInfoHydratorFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return object|ObjectProperty
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $hydrator = new ObjectProperty();
        $hydrator->addStrategy('prices', new KoronaPricesStrategy());
        $hydrator->addStrategy('stock', new KoronaStockStrategy());

        return $hydrator;
    }
}