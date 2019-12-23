<?php declare(strict_types=1);

namespace Boolfly\Brand\Controller\View;

use Boolfly\Brand\Model\BrandFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Brand constructor.
     * @param Context $context
     * @param BrandFactory $brandFactory
     * @param Registry $registry
     * @param Resolver $layerResolver
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        BrandFactory $brandFactory,
        Registry $registry,
        Resolver $layerResolver,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->brandFactory = $brandFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->layerResolver = $layerResolver;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->layerResolver->create('brand');
        $brandId = $this->getRequest()->getParam('id');
        $brand = $this->brandFactory->create()->load($brandId);
        if (!$brand->getId()) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $this->registry->register('current_brand', $brand);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($brand->getName());
        return $resultPage;
    }
}
