<?php

namespace ApiClient\KoronaAuto\Filter;

use Nomenclature\Product\DTO\ProductTO;
use ApiClient\KoronaAuto\Exception\InvalidArgumentException;
use ApiClient\KoronaAuto\Exception\RuntimeException;
use Nomenclature\ProductWarehouse\DTO\ProductWarehouseTO;
use Nomenclature\Product\Filter\SkuClean;
use Zend\Filter\FilterInterface;

/**
 * Class SuitableProducts
 *
 * @package ApiClient\KoronaAuto\Filter
 */
class SuitableProducts implements FilterInterface
{
    /**
     * @var string[]|null
     */
    protected $partnums;

    /**
     * @var string[]|null
     */
    protected $manufacturers;

    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * SuitableProducts constructor.
     *
     * @param FilterInterface $filter
     */
    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param string[] $partnums
     * @param string[] $manufacturers
     *
     * @return $this
     */
    public function __invoke(array $partnums, array $manufacturers)
    {
        if (count($partnums)) {
            $this->partnums = $partnums;
        }

        if (count($manufacturers)) {
            $this->manufacturers = $manufacturers;
        }

        return $this;
    }

    /**
     * @param mixed|ProductTO[] $values
     *
     * @return ProductTO[]
     * @throws RuntimeException|InvalidArgumentException
     * @todo check for price and stock
     */
    public function filter($values): array
    {
        if ($this->partnums === null || $this->manufacturers === null) {
            throw new RuntimeException(sprintf('"%s" must be invoked first passing partnums and manufacturers', self::class));
        }

        if (!is_array($values)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Given "value" must be an array of %s instances, but not %s', ProductTO::class,
                    is_object($values) ? get_class($values) : gettype($values)));
        }

        /** @var SkuClean $filter */
        $filter = $this->filter;
        $return = [];
        foreach ($values as $key => $item) {
            if (!$item instanceof ProductTO) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Each element of given array must be an instance of %s, but not %s', ProductTO::class,
                        is_object($item) ? get_class($item) : gettype($item)));
            }

            $sku = $filter->filter($item->getSku());

            if (($key = array_search($sku, $this->partnums, true)) === false) {
                continue;
            }

            if ($item->getManufacturer() !== null && $item->getManufacturer()->getTitle() === $this->manufacturers[$key]) {
                $return[] = $item;
            }
        }

        return $this->filterByPriceAndStock($return);
    }

    /**
     * @param ProductTO[] $value
     *
     * @return ProductTO[]
     */
    protected function filterByPriceAndStock(array $value): array
    {
        $return = [];
        foreach ($value as $item) {
            if (!$item instanceof ProductTO) {
                continue;
            }

            /** @var ProductWarehouseTO $productWarehouse */
            $productWarehouse = $item->getProductWarehouses()->first();
            if ($productWarehouse->getInStock() && $productWarehouse->getPrices()->count()) {
                $return[] = $item;
            }
        }

        return $return;
    }
}