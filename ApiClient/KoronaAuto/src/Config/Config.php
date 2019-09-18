<?php

namespace ApiClient\KoronaAuto\Config;

use Zend\Stdlib\AbstractOptions;

/**
 * Class Config
 *
 * @package ApiClient\KoronaAuto\Config
 */
class Config extends AbstractOptions
{
    /**
     * @var array
     */
    protected $cache;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $sourceName;

    /**
     * @var string
     */
    protected $assembleService;

    /**
     * @var int
     */
    protected $threadsNum = 10;

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param $baseUrl
     *
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiUid()
    {
        return $this->apiUid;
    }

    /**
     * @param string $apiUid
     *
     * @return $this
     */
    public function setApiUid($apiUid)
    {
        $this->apiUid = $apiUid;

        return $this;
    }

    /**
     * @return array
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param array $cache
     *
     * @return $this
     */
    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }

    /**
     * @param string $sourceName
     *
     * @return $this
     */
    public function setSourceName(string $sourceName)
    {
        $this->sourceName = $sourceName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssembleService()
    {
        return $this->assembleService;
    }

    /**
     * @param string $assembleService
     *
     * @return $this
     */
    public function setAssembleService(string $assembleService)
    {
        $this->assembleService = $assembleService;

        return $this;
    }

    /**
     * @return int
     */
    public function getThreadsNum(): int
    {
        return $this->threadsNum;
    }

    /**
     * @param int $threadsNum
     *
     * @return $this
     */
    public function setThreadsNum(int $threadsNum): self
    {
        $this->threadsNum = $threadsNum;

        return $this;
    }
}