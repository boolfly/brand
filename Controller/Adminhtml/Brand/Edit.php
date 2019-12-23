<?php declare(strict_types=1);

namespace Boolfly\Brand\Controller\Adminhtml\Brand;

use Boolfly\Brand\Model\BrandFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Boolfly_Brand::save';
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param BrandFactory $brandFactory
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        BrandFactory $brandFactory,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->brandFactory = $brandFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }


    public function execute()
    {
        $entityId = $this->getRequest()->getParam('entity_id');
        $model = $this->brandFactory->create();
        if ($entityId) {
            $model->load($entityId);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This brand no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        //Set entered data if was error when do save
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('bf_brand', $model);
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Boolfly_Brand::brand')
            ->addBreadcrumb(__('Boolfly Brand'), __('Brand'))
            ->addBreadcrumb(__('Manage Brand'), __('Manage Brand'));
        $resultPage->getConfig()->getTitle()->prepend(__('Boolfly Brand'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Brand'));
        return $resultPage;
    }
}
