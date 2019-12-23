<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\ResourceModel;

class RedundantBrandImageChecker
{
    /**
     * @var Brand\CollectionFactory
     */
    private $collectionFactory;

    /**
     * RedundantBrandImageChecker constructor.
     * @param Brand\CollectionFactory $collectionFactory
     */
    public function __construct(
        Brand\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $imageName
     * @return bool
     */
    public function execute(string $imageName): bool
    {
        $brands = $this->collectionFactory->create()->addFieldToFilter('image', $imageName);
        return empty($brands->getSize());
    }
}
