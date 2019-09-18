<?php

namespace ApiClient\KoronaAuto\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;
use stdClass;

/**
 * Class KoronaStockStrategy
 *
 * @package ApiClient\KoronaAuto\Hydrator\Strategy
 */
class KoronaStockStrategy implements StrategyInterface
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
            if ($item instanceof stdClass && isset($item->warehouse->name, $item->warehouse->quantity)
                && preg_match('/(спб|петербург|питер)/ui', $item->warehouse->name)) {
                return (int)$item->warehouse->quantity;
            }
        }

        return 0;
    }

    public function hydrate($value)
    {
    }
}