<?php

namespace ApiClient\KoronaAuto\Service\Factory;

use ApiClient\KoronaAuto\Config\Config;
use ApiClient\KoronaAuto\Filter\SuitableProducts;
use Interop\Container\ContainerInterface;
use Zend\Filter\FilterPluginManager;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AssembleServiceFactory
 *
 * @package ApiClient\KoronaAuto\Service\Factory
 */
class AssembleServiceFactory implements FactoryInterface
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
        /** @var Config $config */
        $config = $container->get(Config::class);

        /** @var HydratorPluginManager $hydratorManager */
        $hydratorManager = $container->get('HydratorManager');

        /** @var FilterPluginManager $filterManager */
        $filterManager = $container->get('FilterManager');

        /** @var SuitableProducts $filter */
        $filter = $filterManager->get(SuitableProducts::class);

        return new $requestedName($config, $hydratorManager, $filter);
    }
}