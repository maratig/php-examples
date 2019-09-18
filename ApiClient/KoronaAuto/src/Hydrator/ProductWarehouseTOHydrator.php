<?php

namespace ApiClient\KoronaAuto\Hydrator;

use Nomenclature\Product\DTO\ProductTO;
use Nomenclature\ProductPrice\DTO\ProductPriceTO;
use Nomenclature\ProductPrice\Filter\ProductPriceFilter;
use Nomenclature\ProductWarehouse\DTO\ProductWarehouseTO;
use Nomenclature\Supplier\DTO\SupplierTO;
use Nomenclature\Warehouse\DTO\WarehouseTO;
use Util\Guard\ObjectGuardTrait;
use Zend\Hydrator\HydratorInterface;

/**
 * Class ProductWarehouseTOHydrator
 *
 * @package ApiClient\KoronaAuto\Hydrator
 */
class ProductWarehouseTOHydrator implements HydratorInterface
{
    use ObjectGuardTrait;

    public function extract($object)
    {
    }

    /**
     * @param array     $data
     * @param ProductTO $object
     *
     * @return ProductTO
     */
    public function hydrate(array $data, $object)
    {
        $this->guardForProperInstance($object, ProductTO::class);

        if (!isset($data['id']) || !isset($data['stock']) || !isset($data['prices']) || empty($data['supplier'])) {
            return $object;
        }

        $productWarehouse = $this->createWarehouse($data['id']);
        $productWarehouse->setInStock($data['stock']);
        $this->addPrice($productWarehouse, $data['prices']);
        $this->addSupplier($productWarehouse->getWarehouse(), $data['supplier']);
        $object->addProductWarehouse($productWarehouse);

        return $object;
    }

    /**
     * @param string $id
     *
     * @return ProductWarehouseTO
     */
    protected function createWarehouse(string $id)
    {
        $warehouseTO = new WarehouseTO();
        $warehouseTO->setTitle('Корона Авто')
            ->setAlias('korona-auto');// TODO maybe it is better to get these values from config
        $productWarehouseTO = new ProductWarehouseTO();
        $productWarehouseTO->setWarehouse($warehouseTO)
            ->setSupplierProductId($id);

        return $productWarehouseTO;
    }

    /**
     * @param ProductWarehouseTO $dto
     * @param int                $price
     *
     * @return void
     */
    protected function addPrice(ProductWarehouseTO $dto, int $price)
    {
        $priceTO = new ProductPriceTO();
        $priceTO->setPrice($price * 100)
            ->setType(ProductPriceFilter::TYPE_PURCHASE);// TODO set currency/valute as well
        $dto->addPrice($priceTO);
    }

    /**
     * @param WarehouseTO $dto
     * @param string      $supplier
     *
     * @return void
     */
    protected function addSupplier(WarehouseTO $dto, string $supplier)
    {
        $supplierTO = new SupplierTO();
        $supplierTO->setAlias($supplier);
        $dto->setSupplier($supplierTO);
    }
}