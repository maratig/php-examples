<?php

namespace ApiClient\KoronaAuto\Filter\Factory;

use Interop\Container\ContainerInterface;
use Nomenclature\Product\Filter\SkuClean;
use Zend\Filter\FilterPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class SuitableProductsFactory
 *
 * @package ApiClient\KoronaAuto\Filter\Factory
 */
class SuitableProductsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return mixed|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var FilterPluginManager $filterManager */
        $filterManager = $container->get('FilterManager');

        /** @var SkuClean $filter */
        $filter = $filterManager->get(SkuClean::class);

        return new $requestedName($filter);
    }
}