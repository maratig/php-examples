<?php

namespace ApiClient\Base\Service;

use ApiClient\Base\DTO\ClientUnit;
use SplObjectStorage;

/**
 * Trait AssembleServiceTrait
 *
 * @package ApiClient\Base\Service
 */
trait AssembleServiceTrait
{
    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return ClientUnit|null
     */
    protected function getMyClientUnit(SplObjectStorage $clientUnits): ?ClientUnit
    {
        /** @var ClientUnit $clientUnit */
        foreach ($clientUnits as $clientUnit) {
            if ($clientUnit->getClient() === static::class) {
                return $clientUnit;
            }
        }

        return null;
    }
}