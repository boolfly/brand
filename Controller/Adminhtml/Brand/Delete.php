<?php declare(strict_types=1);

namespace Boolfly\Brand\Controller\Adminhtml\Brand;

use Boolfly\Brand\Model\BrandFactory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Boolfly_Brand::save';

    /**
     * @var BrandFactory
     */
    private $brandFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param BrandFactory $brandFactory
     */
    public function __construct(
        Action\Context $context,
        BrandFactory $brandFactory
    ) {
        $this->brandFactory = $brandFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $entityId = $this->getRequest()->getParam('entity_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($entityId) {
            try {
                $model = $this->brandFactory->create();
                $model->load($entityId);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The brand has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $entityId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a brand to delete.'));
        return $resultRedirect->setPath('*/*');
    }
}
