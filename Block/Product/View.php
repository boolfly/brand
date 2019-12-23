<?php declare(strict_types=1);

namespace Boolfly\Brand\Block\Product;

use Boolfly\Brand\Helper\Image as HelperImage;
use Boolfly\Brand\Model\BrandFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class View extends Template
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var HelperImage
     */
    private $helperImage;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param HelperImage $helperImage
     * @param BrandFactory $brandFactory
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        HelperImage $helperImage,
        BrandFactory $brandFactory,
        Registry $registry,
        array $data = []
    ) {
        $this->helperImage = $helperImage;
        $this->brandFactory = $brandFactory;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return DataObject|null
     * @throws LocalizedException
     */
    public function getBrand()
    {
        $product = $this->getProduct();
        if (null !== $product->getCustomAttribute('brand')) {
            $brandId = $product->getCustomAttribute('brand')->getValue();
            $brand = $this->brandFactory->create()->load($brandId);
            if ($brand->getId()) {
                if (!$brand->getVisibility()) {
                    return null;
                }
                $image = $this->helperImage->resize($brand->getData('image'));
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
