<?php

namespace ApiClient\Base\Service;

use Nomenclature\Product\DTO\ProductTO;
use SplObjectStorage;

/**
 * Interface ApiUriAssemblerInterface
 *
 * @package ApiClient\Base\Service
 */
interface ApiUriAssemblerInterface
{
    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return void
     */
    public function assemblePrepareRequest(SplObjectStorage $clientUnits): void;

    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return void
     */
    public function assembleSearchRequest(SplObjectStorage $clientUnits): void;

    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return ProductTO[]|null
     */
    public function makeResults(SplObjectStorage $clientUnits): ?array;
}