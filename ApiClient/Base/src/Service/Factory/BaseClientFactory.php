<?php

namespace ApiClient\Base\Service\Factory;

use ApiClient\Base\Service\ApiUriAssemblerInterface;
use GuzzleHttp\Client;
use Interop\Container\ContainerInterface;
use Nomenclature\Product\Service\ProductManageService;
use Zend\Cache\Storage\StorageInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;
use ApiClient\Base\Exception\BadConfigurationException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class BaseClientFactory
 *
 * @package ApiClient\Base\Service\Factory
 */
class BaseClientFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|ServiceManager $container
     * @param string                            $requestedName
     * @param array|null                        $options
     *
     * @return mixed|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var array $config */
        $config = $container->get('config');

        if (!isset($config['api-client']) || !is_array($config['api-client'])) {
            throw new BadConfigurationException('Configuration for api-clients not found');
        }

        $config = $config['api-client'];

        if (!isset($config['base']) || !is_array($config['base'])) {
            throw new BadConfigurationException('Base api-client config is not set or not an array');
        }

        if (!isset($config['base']['cache']) || !is_array($config['base']['cache'])) {
            throw new BadConfigurationException('Cache for Base api-client is not set or not an array');
        }

        /** @var StorageInterface $storage */
        $storage    = $container->build('StorageInterface', ['cache' => $config['base']['cache']]);
        $httpClient = new Client();
        $apiClients = [];
        foreach ($config as $client) {
            if (isset($client['alias'], $client['assembleService']) && $client['alias'] !== 'base') {
                /** @var ApiUriAssemblerInterface $clientService */
                $apiClients[] = $container->get($client['assembleService']);
            }
        }

        /** @var ProductManageService $productManageService */
        $productManageService = $container->get(ProductManageService::class);

        /** @var HydratorPluginManager $hydratorManager */
        $hydratorManager = $container->get('HydratorManager');

        return new $requestedName($storage, $httpClient, $apiClients, $productManageService, $hydratorManager);
    }
}