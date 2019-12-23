<?php declare(strict_types=1);

namespace Boolfly\Brand\Model\Layer\Brand;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class CollectionFilter implements CollectionFilterInterface
{
    /**
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Config $catalogConfig
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param Visibility $productVisibility
     */
    public function __construct(
        Config $catalogConfig,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Visibility $productVisibility
    ) {
        $this->catalogConfig = $catalogConfig;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->productVisibility = $productVisibility;
    }

    /**
     * Filter product collection
     *
     * @param Collection $collection
     * @param Category $category
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws NoSuchEntityException
     */
    public function filter(
        $collection,
        Category $category
    ) {
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->setStore($this->storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInSearchIds());
        if ($this->registry->registry('current_brand')) {
            $collection->addAttributeToFilter('brand', $this->registry->registry('current_brand')->getId());
        };
    }
}
