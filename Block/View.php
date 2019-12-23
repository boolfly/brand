<?php declare(strict_types=1);

namespace Boolfly\Brand\Block;

use Boolfly\Brand\Helper\Image as HelperImage;
use Boolfly\Brand\Model\Brand;
use Boolfly\Brand\Model\BrandFactory;
use Exception;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Cms\Model\Template\FilterProvider;

class View extends Template implements IdentityInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * @var HelperImage
     */
    private $helperImage;

    /**
     * Catalog layer
     *
     * @var Layer
     */
    protected $catalogLayer;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param HelperImage $helperImage
     * @param BrandFactory $brandFactory
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        HelperImage $helperImage,
        BrandFactory $brandFactory,
        LayerResolver $layerResolver,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->helperImage = $helperImage;
        $this->registry = $registry;
        $this->filterProvider = $filterProvider;
        $this->brandFactory = $brandFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return Brand|null
     */
    public function getBrand()
    {
        if (!$this->registry->registry('current_brand')) {
            $id = $this->getRequest()->getParam('id');
            return $this->brandFactory->create()->load($id);
        }
        return $this->registry->registry('current_brand');
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('product_list');
    }

    /**
     * @param string $value
     * @return string
     * @throws Exception
     */
    public function getCmsFilterContent($value = '')
    {
        $html = $this->filterProvider->getPageFilter()->filter($value);
        return $html;
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return $this->getBrand()->getIdentities();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImage()
    {
        $image = $this->getBrand()->getData('image');
        return $this->helperImage->resize($image, 250, 250);
    }

    protected function _toHtml()
    {
        if (!$this->getBrand()->getVisibility()) {
            return '';
        }
        return parent::_toHtml();
    }
}
