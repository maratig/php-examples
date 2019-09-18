<?php

namespace ApiClient\KoronaAuto\Service;

use ApiClient\Base\Service\ApiUriAssemblerInterface;
use ApiClient\Base\Service\AssembleServiceTrait;
use ApiClient\KoronaAuto\Config\Config;
use ApiClient\KoronaAuto\Filter\SuitableProducts;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use SplObjectStorage;
use Util\Helper\Url;
use Zend\Filter\FilterInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\ObjectProperty;
use Zend\Json\Json;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Nomenclature\Product\DTO\ProductTO;
use Exception;

/**
 * Class AssembleService
 *
 * @package ApiClient\KoronaAuto\Service
 */
class AssembleService implements ApiUriAssemblerInterface
{
    use AssembleServiceTrait;

    protected const INFO_QUERY   = '/product/info/?id=%s&apiUid=%s&dataType=json';
    protected const SEARCH_QUERY = '/search/?q=%s&apiUid=%s&dataType=json';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ContainerInterface
     */
    protected $hydratorManager;

    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * AssembleService constructor.
     *
     * @param Config             $config
     * @param ContainerInterface $hydratorManager
     * @param FilterInterface    $filter
     */
    public function __construct(Config $config, ContainerInterface $hydratorManager, FilterInterface $filter)
    {
        $this->config          = $config;
        $this->hydratorManager = $hydratorManager;
        $this->filter          = $filter;
    }

    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return void
     */
    public function assemblePrepareRequest(SplObjectStorage $clientUnits): void
    {
        if (($clientUnit = $this->getMyClientUnit($clientUnits)) === null) {
            return;
        }

        if ($clientUnit->getPartnums() === null) {
            return;
        }

        $partnums = array_slice($clientUnit->getPartnums(), 0, $this->config->getThreadsNum());

        foreach ($partnums as $partnum) {
            $uri = $this->config->getBaseUrl() . sprintf(
                    self::SEARCH_QUERY, $partnum, $this->config->getApiUid());
            $clientUnit->setPrepareItem($uri);
        }
    }

    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return void
     */
    public function assembleSearchRequest(SplObjectStorage $clientUnits): void
    {
        if (($clientUnit = $this->getMyClientUnit($clientUnits)) === null || !count($clientUnit->getPrepareItems())) {
            return;
        }

        $products = [[]];
        /** @var ResponseInterface $prepareItem */
        foreach ($clientUnit->getPrepareItems() as $prepareItem) {
            try {
                $data = Json::decode($prepareItem->getBody());
            } catch (Exception $ex) {
                continue;
            }

            if (!isset($data->product) || !is_array($data->product)) {
                continue;
            }

            $products[] = $data->product;
        }
        $products = array_merge(...$products);

        $doneId = [];
        foreach ($products as $product) {
            if (!isset($product->id) || in_array($product->id, $doneId, true)) {
                continue;
            }

            $doneId[] = $product->id;
            $uri      =
                $this->config->getBaseUrl() . sprintf(self::INFO_QUERY, $product->id, $this->config->getApiUid());
            $clientUnit->setSearchItem($uri);
        }
    }

    /**
     * @param SplObjectStorage $clientUnits
     *
     * @return ProductTO[]|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function makeResults(SplObjectStorage $clientUnits): ?array
    {
        $return = [];
        if (($clientUnit = $this->getMyClientUnit($clientUnits)) === null || !count($clientUnit->getSearchItems())) {
            return $return;
        }

        /** @var ObjectProperty $extractor */
        $extractor = $this->hydratorManager->get('KoronaInfoHydrator');

        /** @var ClassMethods $hydrator */
        $hydrator = $this->hydratorManager->get('ProdTOHydrator');

        /** @var ResponseInterface $searchItem */
        foreach ($clientUnit->getSearchItems() as $uri => $searchItem) {
            if (!$searchItem instanceof ResponseInterface) {
                continue;
            }

            try {
                $product = Json::decode($searchItem->getBody());
            } catch (Exception $ex) {
                continue;// TODO maybe it is better to log such errors
            }

            if (!isset($product->product)) {
                continue;
            }

            $data             = $extractor->extract($product->product);
            $data['supplier'] = $this->config->getSourceName();
            $data['id']       = Url::getParamValue($uri, 'id');

            /** @var ProductTO $productTO */
            $productTO = $hydrator->hydrate($data, new ProductTO());
            $return[]  = $productTO;
        }

        /** @var SuitableProducts $filter */
        $filter = $this->filter;
        $return = $filter($clientUnit->getPartnums(), $clientUnit->getManufacturers())->filter($return);

        return $return;
    }
}