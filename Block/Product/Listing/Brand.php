<?php declare(strict_types=1);

namespace Boolfly\Brand\Block\Product\Listing;

use Boolfly\Brand\Helper\Image as HelperImage;
use Boolfly\Brand\Model\BrandFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

class Brand extends Template
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * @var HelperImage
     */
    private $helperImage;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * Brand constructor.
     * @param Template\Context $context
     * @param HelperImage $helperImage
     * @param ProductFactory $productFactory
     * @param BrandFactory $brandFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperImage $helperImage,
        ProductFactory $productFactory,
        BrandFactory $brandFactory,
        array $data = []
    ) {
        $this->helperImage = $helperImage;
        $this->brandFactory = $brandFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->getParentBlock()->getProduct();
    }

    /**
     * @return DataObject|null
     * @throws LocalizedException
     */
    public function getBrand()
    {
        $product = $this->productFactory->create();
        $product->load($this->getProduct()->getId());
        if (null !== $product->getCustomAttribute('brand')) {
            $brandId = $product->getCustomAttribute('brand')->getValue();
            $brand = $this->brandFactory->create()->load($brandId);
            if ($brand->getId()) {
                if (!$brand->getVisibility()) {
                    return null;
                }
                $image = $this->helperImage->resize($brand->getData('image'), 40, 40);
                return new DataObject(
                    [
                        'name' => $brand->getName(),
                        'url' => $this->_urlBuilder->getDirectUrl($brand->getUrlKey()),
                        'image' => $image
                    ]
                );
            }
        }
        return null;
    }

    protected function _toHtml()
    {
        if (null === $this->getBrand()) {
            return '';
        }
        return parent::_toHtml();
    }
}
