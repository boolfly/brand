<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\ResourceModel\Brand;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            'Boolfly\Brand\Model\Brand',
            'Boolfly\Brand\Model\ResourceModel\Brand'
        );
    }
}
