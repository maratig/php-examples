<?php

namespace ApiClient\KoronaAuto\Hydrator;

use Nomenclature\Product\DTO\ProductTO;
use Util\Guard\ObjectGuardTrait;
use Zend\Hydrator\HydratorInterface;

/**
 * Class ProductTOHydrator
 *
 * @package ApiClient\KoronaAuto\Hydrator
 */
class ProductTOHydrator implements HydratorInterface
{
    use ObjectGuardTrait;

    /**
     * @param array     $data
     * @param ProductTO $object
     *
     * @return ProductTO
     */
    public function hydrate(array $data, $object)
    {
        $this->guardForProperInstance($object, ProductTO::class);

        if (!empty($data['name'])) {
            $object->setTitle($data['name']);
        }

        if (!empty($data['factory_number'])) {
            $object->setSku($data['factory_number']);
        }

        if (!empty($data['original_number'])) {
            $oems = explode(',', $data['original_number']);
            $oems = array_map('trim', $oems);
            $object->setPartnumber(implode('/', $oems));
        }

        return $object;
    }

    public function extract($object)
    {
    }
}