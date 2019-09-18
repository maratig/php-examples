<?php

namespace ApiClient\KoronaAuto\Config\Factory;

use Interop\Container\ContainerInterface;
use ApiClient\KoronaAuto\Exception\BadConfigurationException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ClientConfigFactory
 *
 * @package ApiClient\Config\Factory
 */
class ConfigFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return mixed|object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws BadConfigurationException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var array $config */
        $config = $container->get('config');

        if (!isset($config['api-client']['korona-auto']) || !\is_array($config['api-client']['korona-auto'])) {
            throw new BadConfigurationException('Client config is not set or not an array');
        }

        /** @var array $koronaConfig */
        $koronaConfig = $config['api-client']['korona-auto'];

        if (!isset($koronaConfig['baseUrl'])) {
            throw new BadConfigurationException('KoronaAuto config must contain "baseUrl" parameter');
        }

        if (filter_var($koronaConfig['baseUrl'], FILTER_VALIDATE_URL) === false) {
            throw new BadConfigurationException('Parameter "baseUrl" must be a valid URL');
        }

        if (!isset($koronaConfig['apiUid'])) {
            throw new BadConfigurationException('KoronaAuto config must contain "apiUid" parameter');
        }

        if (!isset($koronaConfig['cache'])) {
            throw new BadConfigurationException('Cache is not set in client configuration');
        }

        if (!isset($koronaConfig['alias'])) {
            throw new BadConfigurationException('Configuration does not contain alias key');
        }

        return new $requestedName($koronaConfig);
    }
}