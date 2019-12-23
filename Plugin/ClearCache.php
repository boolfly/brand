<?php declare(strict_types=1);

namespace Boolfly\Brand\Plugin;

use Boolfly\Brand\Model\ResourceModel\Brand as BrandResource;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Model\AbstractModel;

class ClearCache
{
    /**
     * Application Cache Manager
     *
     * @var CacheInterface
     */
    protected $cacheManager;

    /**
     * ClearCache constructor.
     * @param CacheInterface $cacheManager
     */
    public function __construct(
        CacheInterface $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param BrandResource $subject
     * @param $result
     * @return BrandResource
     */
    public function afterSave(BrandResource $subject, $result): BrandResource
    {
        $this->cleanCache();
        return $result;
    }

    public function afterDelete(BrandResource $subject, $result, AbstractModel $brand): BrandResource
    {
        $this->cleanCache();
        return $result;
    }

    private function cleanCache()
    {
        $tags = ['BF_BRAND_ALL_BRANDS'];
        $this->cacheManager->clean($tags);
    }
}
