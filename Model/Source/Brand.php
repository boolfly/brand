<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Boolfly\Brand\Model\ResourceModel\Brand\CollectionFactory;

class Brand extends AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $brandsFactory;

    /**
     * Brand constructor.
     * @param CollectionFactory $brandsFactory
     */
    public function __construct(
        CollectionFactory $brandsFactory
    ) {
        $this->brandsFactory = $brandsFactory;
    }


    /**
     * Retrieve All options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (!$this->_options) {
            $this->_options = [];
            $brandsCollection = $this->brandsFactory->create();
            foreach ($brandsCollection as $brand) {
                $this->_options[] = [
                    'label' => $brand->getName(),
                    'value' => $brand->getEntityId()
                ];
            }
        }
        if ($withEmpty) {
            array_unshift($this->_options, ['label' => ' ', 'value' => '']);
        }
        return $this->_options;
    }
}
