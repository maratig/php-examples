<?php

namespace ApiClient\KoronaAuto\Hydrator;

use Nomenclature\Manufacturer\DTO\ManufacturerTO;
use Nomenclature\Product\DTO\ProductTO;
use Util\Guard\ObjectGuardTrait;
use Zend\Hydrator\HydratorInterface;

/**
 * Class ManufacturerTOHydrator
 *
 * @package ApiClient\KoronaAuto\Hydrator
 */
class ManufacturerTOHydrator implements HydratorInterface
{
    use ObjectGuardTrait;

    public function extract($object)
    {
    }

    /**
     * @param array  $data
     * @param ProductTO $object
     *
     * @return ProductTO
     */
    public function hydrate(array $data, $object)
    {
        $this->guardForProperInstance($object, ProductTO::class);

        if (empty($data['producer'])) {
            return $object;
        }

        if (($manufacturer = $object->getManufacturer()) === null || !$manufacturer instanceof ManufacturerTO) {
            $manufacturer = new ManufacturerTO();
        }

        $manufacturer->setTitle($data['producer']);
        $object->setManufacturer($manufacturer);

        return $object;
    }
}