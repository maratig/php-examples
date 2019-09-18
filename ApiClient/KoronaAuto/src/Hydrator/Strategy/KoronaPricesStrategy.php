<?php

namespace ApiClient\KoronaAuto\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;

/**
 * Class KoronaPricesStrategy
 *
 * @package ApiClient\KoronaAuto\Hydrator\Strategy
 */
class KoronaPricesStrategy implements StrategyInterface
{
    /**
     * @param mixed $value
     *
     * @return int|mixed
     */
    public function extract($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        foreach ($value as $item) {
            if (isset($item->warehouse->name, $item->warehouse->value)
                && preg_match('/(спб|петербург|питер)/ui', $item->warehouse->name)) {
                return $item->warehouse->value;
            }
        }

        return 0;
    }

    public function hydrate($value)
    {
    }
}