<?php declare(strict_types=1);

namespace Boolfly\Brand\Plugin;

use Exception;
use Boolfly\Brand\Model\Brand\File;
use Boolfly\Brand\Model\ImageUploader;
use Boolfly\Brand\Model\ResourceModel\RedundantBrandImageChecker;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Model\AbstractModel;
use Boolfly\Brand\Model\ResourceModel\Brand as BrandResource;
use Psr\Log\LoggerInterface;

class ImageProcessingPlugin
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var File
     */
    private $file;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RedundantBrandImageChecker
     */
    private $redundantBrandImageChecker;

    /**
     * RemoveImagePlugin constructor.
     * @param File $file
     * @param ImageUploader $imageUploader
     * @param RedundantBrandImageChecker $redundantBrandImageChecker
     * @param LoggerInterface $logger
     */
    public function __construct(
        File $file,
        ImageUploader $imageUploader,
        RedundantBrandImageChecker $redundantBrandImageChecker,
        LoggerInterface $logger
    ) {
        $this->file = $file;
        $this->imageUploader = $imageUploader;
        $this->redundantBrandImageChecker = $redundantBrandImageChecker;
        $this->logger = $logger;
    }

    /**
     * @param BrandResource $subject
     * @param AbstractModel $brand
     */
    public function beforeSave(BrandResource $subject, AbstractModel $brand)
    {
        $value = $brand->getData('image');
        if ($imageName = $this->getUploadedImageName($value)) {
            $brand->setData('image', $value[0]['name']);
            $brand->setData('image_obj', $value);
        } elseif (!is_string($value)) {
            $brand->setData('image', null);
        }
    }

    /**
     * @param BrandResource $subject
     * @param $result
     * @param AbstractModel $brand
     * @return BrandResource
     * @throws FileSystemException
     */
    public function afterSave(BrandResource $subject, $result, AbstractModel $brand): BrandResource
    {

        $value = $brand->getData('image_obj');
        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
            try {
                $this->imageUploader->moveFileFromTmp($imageName);
            } catch (Exception $e) {
                $this->logger->critical($e);
            }
        }

        //Remove Image
        $originalImage = $brand->getOrigData('image');
        if (null !== $originalImage
            && $originalImage !== $brand->getData('image')
            && $this->redundantBrandImageChecker->execute($originalImage)
        ) {
            $this->file->delete($originalImage);
            $this->file->cleanupCacheImages($originalImage);
        }
        return  $result;
    }

    /**
     * @param BrandResource $subject
     * @param $result
     * @param AbstractModel $brand
     * @return BrandResource
     * @throws FileSystemException
     */
    public function afterDelete(BrandResource $subject, $result, AbstractModel $brand): BrandResource
    {
        $image = $brand->getData('image');
        if ($image && $this->redundantBrandImageChecker->execute($image)) {
            $this->file->delete($image);
            $this->file->cleanupCacheImages($image);
        }
        return $result;
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value
     * @return bool
     */
    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }
}
