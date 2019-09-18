<?php

namespace ApiClient\Base\Service;

use Nomenclature\Product\DTO\ProductTO;
use Zend\EventManager\EventManagerInterface;

/**
 * Interface ApiClientInterface
 *
 * @package ApiClient\Base\Service
 */
interface ApiClientInterface
{
    /**
     * Get an array of ProductTO objects by given list of partnumbers and corresponding manufacturers
     *
     * @param string[] $partnums
     * @param string[] $manufacturers
     *
     * @return ProductTO[]|null
     */
    public function getFromCache(array $partnums, array $manufacturers): ?array;

    /**
     * @param string[] $partnums
     * @param string[] $manufacturers
     *
     * @return void
     */
    public function apiCall(array $partnums, array $manufacturers): void;

    /**
     * @return EventManagerInterface
     */
    public function getEventManager();
}