<?php

namespace ApiClient\Base\Service;

use ApiClient\Base\DTO\ClientUnit;
use GuzzleHttp\Client;
use function GuzzleHttp\Promise\all;
use Nomenclature\Product\Service\ProductManageService;
use Util\Storage\StorageHelperTrait;
use Zend\Cache\Storage\StorageInterface;
use Zend\EventManager\EventManagerAwareTrait;
use SplObjectStorage;
use Nomenclature\Product\DTO\ProductTO;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\QueryException;

/**
 * Class BaseClient
 *
 * @package ApiClient\Base\Service
 */
class BaseClient implements ApiClientInterface
{
    protected const REQUESTS_PER_SECOND  = 4;
    protected const REQUEST_TYPE_PREPARE = 'prepare';
    protected const REQUEST_TYPE_SEARCH  = 'search';

    use StorageHelperTrait;
    use EventManagerAwareTrait;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var ApiUriAssemblerInterface[]
     */
    protected $apiWorkers;

    /**
     * @var ProductManageService
     */
    protected $productManageService;

    /**
     * BaseClient constructor.
     *
     * @param StorageInterface     $storage
     * @param Client               $client
     * @param array                $apiWorkers
     * @param ProductManageService $productManageService
     */
    public function __construct(StorageInterface $storage, Client $client, array $apiWorkers,
        ProductManageService $productManageService)
    {
        $this->storage              = $storage;
        $this->httpClient           = $client;
        $this->apiWorkers           = $apiWorkers;
        $this->productManageService = $productManageService;
    }

    /**
     * Return an array of product ids. Empty array can be also returned
     *
     * @param string[] $partnums
     * @param string[] $manufacturers
     *
     * @return int[]|null
     */
    public function getFromCache(array $partnums, array $manufacturers): ?array
    {
        $cacheKey = $this->makeStorageKey($partnums, $manufacturers);

        return $this->storage->hasItem($cacheKey) ? unserialize($this->storage->getItem($cacheKey),
            ['allowed_classes' => false]) : null;
    }

    /**
     * Sort the arrays of partnumbers and manufacturers
     *
     * @param array $partnums
     * @param array $manufacturers
     *
     * @return string
     */
    protected function makeStorageKey(array $partnums, array $manufacturers): string
    {
        sort($partnums);
        sort($manufacturers);

        return $this->getStorageKey(__CLASS__, __FUNCTION__, [$partnums, $manufacturers]);
    }

    /**
     * @param array $partnumbers
     * @param array $manufacturers
     *
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function apiCall(array $partnumbers, array $manufacturers): void
    {
        // Create client units and assemble prepare requests
        $clientUnits = new SplObjectStorage();
        foreach ($this->apiWorkers as $worker) {
            $unit = new ClientUnit();
            $unit->setPartnums($partnumbers)
                 ->setManufacturers($manufacturers)
                 ->setClient(get_class($worker));
            $clientUnits->attach($unit);
            $worker->assemblePrepareRequest($clientUnits);
        }

        $this->makeAndExecuteRequests($clientUnits, self::REQUEST_TYPE_PREPARE);

        // Process prepare responses and assemble search requests
        foreach ($this->apiWorkers as $worker) {
            $worker->assembleSearchRequest($clientUnits);
        }

        $this->makeAndExecuteRequests($clientUnits, self::REQUEST_TYPE_SEARCH);

        $productTOs = [[]];
        foreach ($this->apiWorkers as $apiClient) {
            if (($results = $apiClient->makeResults($clientUnits)) !== null) {
                $productTOs[] = $results;
            }
        }
        $productTOs = array_merge(...$productTOs);
        $ids        = $this->processData($productTOs);
        unset($productTOs);

        // Write data to cache
        $cacheKey = $this->makeStorageKey($partnumbers, $manufacturers);
        $this->storage->setItem($cacheKey, serialize($ids));
    }

    /**
     * Save dtos to database and return ids of saved/updated entities
     *
     * @param ProductTO[] $productTOs
     *
     * @return int[]
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    protected function processData(array $productTOs): array
    {
        $return = [];
        foreach ($productTOs as $productTO) {
            $product  = $this->productManageService->createOrUpdateFromDTO($productTO);
            $return[] = $product->getId();
        }

        return $return;
    }

    /**
     * @param SplObjectStorage $clientUnits
     * @param string           $requestType
     *
     * @return void
     */
    protected function makeAndExecuteRequests(SplObjectStorage $clientUnits, string $requestType): void
    {
        $get = 'get' . ucfirst($requestType) . 'Items';
        // Create groups of requests. The size of every group is limited by REQUESTS_PER_SECOND
        $urlGroups = [];
        /** @var ClientUnit $clientUnit */
        foreach ($clientUnits as $clientUnit) {
            $index = $added = 0;
            if (!$clientUnit->$get()->count()) {
                continue;
            }

            foreach ($clientUnit->$get() as $key => $item) {
                $urlGroups[$index][] = $key;
                $added++;

                if ($added === self::REQUESTS_PER_SECOND) {
                    $added = 0;
                    $index++;
                }
            }
        }

        // Send requests
        $lastCallTime = microtime(true) - 1.0;
        foreach ($urlGroups as $urls) {
            $promises = $keys = [];
            foreach ($urls as $url) {
                $keys[]     = $url;
                $promises[] = $this->httpClient->requestAsync('GET', $url);
            }

            $this->pause($lastCallTime);
            $this->doCall($clientUnits, $promises, $keys, $requestType);
            $lastCallTime = microtime(true);
        }
    }

    /**
     * @param float $lastCallTime
     *
     * @return void
     */
    protected function pause(float $lastCallTime): void
    {
        $timeToSleep = (1.0 - (microtime(true) - $lastCallTime)) * 1000000;

        if ($timeToSleep > 0) {
            usleep((int)$timeToSleep);
        }
    }

    /**
     * @param SplObjectStorage $clientUnits
     * @param array            $promises
     * @param array            $keys
     * @param string           $itemPrefix
     *
     * @return void
     */
    protected function doCall(SplObjectStorage $clientUnits, array $promises, array $keys, string $itemPrefix): void
    {
        all($promises)->then(
            function (array $responses) use ($clientUnits, $keys, $itemPrefix)
            {
                $has       = 'has' . ucfirst($itemPrefix) . 'Item';
                $set       = 'set' . ucfirst($itemPrefix) . 'Item';
                $responses = array_combine($keys, $responses);
                foreach ($responses as $key => $response) {
                    /** @var ClientUnit $clientUnit */
                    foreach ($clientUnits as $clientUnit) {
                        if ($clientUnit->$has($key)) {
                            $clientUnit->$set($key, $response);
                            break;
                        }
                    }
                }
            })->wait();
    }
}