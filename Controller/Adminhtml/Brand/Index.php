<?php declare(strict_types=1);

namespace Boolfly\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Brand::brand');
        $resultPage->addBreadcrumb(__('Boolfly Brand'), __('Brand'));
        $resultPage->addBreadcrumb(__('Manage Boolfly Brand'), __('Manage Boolfly Brand'));
        $resultPage->getConfig()->getTitle()->prepend(__('Boolfly Brand'));
        return $resultPage;
    }
}