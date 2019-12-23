<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Visibility implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = \Boolfly\Brand\Model\Brand::getVisibilities();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
