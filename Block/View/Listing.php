<?php declare(strict_types=1);

namespace Boolfly\Brand\Block\View;

use Boolfly\Brand\Helper\Image as HelperImage;
use Boolfly\Brand\Model\Brand;
use Boolfly\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json as Serializer;

class Listing extends Template implements IdentityInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var HelperImage
     */
    private $helperImage;

    /**
     * Listing constructor.
     * @param Template\Context $context
     * @param Serializer $serializer
     * @param HelperImage $helperImage
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Serializer $serializer,
        HelperImage $helperImage,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->helperImage = $helperImage;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->addData(array(
            'cache_lifetime'    => 86400,
            'cache_tags'        => [Brand::CACHE_TAG . '_' . 'all_brands']
        ));
    }

    /**
     * @return array
     */
    public function getBrandsCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('visibility', Brand::STATUS_ENABLED);
        $items = [];
        foreach ($collection as $item) {
            $image = $this->helperImage->resize($item->getData('image'));
            $items[] = [
                'name' => $item->getName(),
                'image'  => $image,
                'url'   => $this->_urlBuilder->getDirectUrl($item->getUrlKey())
            ];
        }
        return $items;
    }

    /**
     * @return bool|false|string
     */
    public function getJsonData()
    {
        return $this->serializer->serialize($this->getBrandsCollection());
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return  [Brand::CACHE_TAG . '_' . 'all_brands'];
    }
}
