<?php

namespace ApiClient\KoronaAuto\Hydrator\Factory;

use Interop\Container\ContainerInterface;
use Util\Hydrator\Strategy\GramWeight;
use Util\Hydrator\Strategy\Partnumber;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorPluginManager;
use Zend\Hydrator\NamingStrategy\MapNamingStrategy;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ProductTOHydratorFactory
 *
 * @package ApiClient\KoronaAuto\Hydrator\Factory
 */
class ProductTOHydratorFactory2 implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return ClassMethods
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var HydratorPluginManager $hydratorManager */
        $hydratorManager = $container->get('HydratorManager');

        /** @var ClassMethods $hydrator */
        $hydrator = $hydratorManager->get(ClassMethods::class);
        $hydrator->setNamingStrategy(
            new MapNamingStrategy(
                ['id'             => 'sourceid', 'name' => 'title', 'producer' => 'manufacturer',
                 'factory_number' => 'sku', 'original_number' => 'partnumber']));
        $hydrator->addStrategy('weight', new GramWeight());
        $hydrator->addStrategy('partnumber', new Partnumber());

        return $hydrator;
    }
}