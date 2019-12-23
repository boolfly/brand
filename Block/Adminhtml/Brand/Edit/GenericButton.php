<?php declare(strict_types=1);

namespace Boolfly\Brand\Block\Adminhtml\Brand\Edit;

use Magento\Backend\Block\Widget\Context;
use Boolfly\Brand\Model\BrandFactory;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BrandFactory
     */
    protected $brandFactory;

    /**
     * @param Context $context
     * @param BrandFactory $brandFactory
     */
    public function __construct(
        Context $context,
        BrandFactory $brandFactory
    ) {
        $this->context = $context;
        $this->brandFactory = $brandFactory;
    }

    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function getBrandId()
    {
        try {
            return $this->brandFactory->create()->load(
                $this->context->getRequest()->getParam('entity_id')
            )->getId();
        } catch (\Exception $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
