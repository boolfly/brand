<?php declare(strict_types=1);

namespace Boolfly\Brand\Helper;

use Boolfly\Brand\Model\Brand\File;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Image\Adapter\AdapterInterface;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractHelper
{
    const WIDTH = 100;
    const HEIGHT = 100;

    /**
     * @var File
     */
    private $file;

    /**
     * @var AdapterFactory
     */
    protected $imageFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Image constructor.
     * @param File $file
     * @param AdapterFactory $imageFactory
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        File $file,
        AdapterFactory $imageFactory,
        StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->file = $file;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $image
     * @param int $width
     * @param int $height
     * @return string
     * @throws NoSuchEntityException
     */
    public function resize($image, $width = self::WIDTH, $height = self::HEIGHT)
    {
        $path = File::ENTITY_MEDIA_PATH . '/cache';
        if ($width !== null) {
            $path .= '/' . $width . 'x';
            if ($height !== null) {
                $path .= $height ;
            }
        }
        $absolutePath = $this->file->getMediaAbsolutePath(File::ENTITY_MEDIA_PATH) . '/' . $image;
        $imageResized = $this->file->getMediaAbsolutePath($path) . '/' . $image;
        if (!$this->file->isFile($path . '/' . $image)) {
            /** @var AdapterInterface $imageFactory */
            $imageFactory = $this->imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->keepFrame(true);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->resize($width, $height);
            $imageFactory->save($imageResized);
        }
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path . '/' . $image;
    }
}
