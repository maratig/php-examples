<?php

namespace ApiClient\Base\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nomenclature\Product\DTO\ProductTO;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ClientUnit
 *
 * @package ApiClient\Base\DTO
 */
class ClientUnit
{
    /**
     * @var string|null
     */
    protected $client;

    /**
     * @var string[]|null
     */
    protected $partnums;

    /**
     * @var string[]|null
     */
    protected $manufacturers;

    /**
     * @var Collection
     */
    protected $prepareItems = [];

    /**
     * @var Collection
     */
    protected $searchItems = [];

    /**
     * @var ProductTO[]
     */
    protected $results;

    /**
     * ClientUnit constructor.
     */
    public function __construct()
    {
        $this->prepareItems = new ArrayCollection();
        $this->searchItems  = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getClient(): ?string
    {
        return $this->client;
    }

    /**
     * @param string $client
     *
     * @return $this
     */
    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getPartnums(): ?array
    {
        return $this->partnums;
    }

    /**
     * @param string[] $partnums
     *
     * @return $this
     */
    public function setPartnums(array $partnums): self
    {
        if (count($partnums)) {
            $this->partnums = $partnums;
        }

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getManufacturers(): ?array
    {
        return $this->manufacturers;
    }

    /**
     * @param string[] $manufacturers
     *
     * @return $this
     */
    public function setManufacturers(array $manufacturers): self
    {
        if (count($manufacturers)) {
            $this->manufacturers = $manufacturers;
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getPrepareItems()
    {
        return $this->prepareItems;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasPrepareItem(string $key): bool
    {
        return $this->prepareItems->containsKey($key);
    }

    /**
     * @param string                 $key
     * @param ResponseInterface|null $response
     *
     * @return $this
     */
    public function setPrepareItem(string $key, ResponseInterface $response = null): self
    {
        $this->prepareItems->set($key, $response);

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getSearchItems()
    {
        return $this->searchItems;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasSearchItem(string $key): bool
    {
        return $this->searchItems->containsKey($key);
    }

    /**
     * @param string                 $key
     * @param ResponseInterface|null $response
     *
     * @return $this
     */
    public function setSearchItem(string $key, ResponseInterface $response = null): self
    {
        $this->searchItems->set($key, $response);

        return $this;
    }

    /**
     * @return ProductTO[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param ProductTO $result
     *
     * @return $this
     */
    public function addResult(ProductTO $result): self
    {
        $this->results = $result;

        return $this;
    }
}