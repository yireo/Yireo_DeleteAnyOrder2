<?php
namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Yireo\DeleteAnyOrder2\Model\Page;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
            
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::delete';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Yireo\DeleteAnyOrder2\Model\Order::STATUS_ENABLED;
            }
            if (empty($data['yireo_deleteanyorder2_order_id'])) {
                $data['yireo_deleteanyorder2_order_id'] = null;
            }

            /** @var Yireo\DeleteAnyOrder2\Model\Order $model */
            $model = $this->_objectManager->create('Yireo\DeleteAnyOrder2\Model\Order');

            $id = $this->getRequest()->getParam('yireo_deleteanyorder2_order_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the thing.'));
                $this->dataPersistor->clear('yireo_deleteanyorder2_order');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['yireo_deleteanyorder2_order_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('yireo_deleteanyorder2_order', $data);
            return $resultRedirect->setPath('*/*/edit', ['yireo_deleteanyorder2_order_id' => $this->getRequest()->getParam('yireo_deleteanyorder2_order_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }    
}
