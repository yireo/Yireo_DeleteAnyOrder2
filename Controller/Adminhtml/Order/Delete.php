<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Yireo\DeleteAnyOrder2\Order\OrderDeleter;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::delete_order';

    /**
     * @param Context $context
     * @param OrderDeleter $orderDeleter
     */
    public function __construct(
        Context $context,
        private readonly OrderDeleter $orderDeleter
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');

        try {
            $this->orderDeleter->delete($orderId);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(__('Unable to remove order: %1', $exception->getMessage()));
            return $this->redirectToOrder($orderId);
        } catch (Exception $exception) {
            $this->messageManager->addExceptionMessage($exception, __('Unable to remove order'));
            return $this->redirectToOrder($orderId);
        }

        $this->messageManager->addNoticeMessage(__('Removed order'));

        return $this->resultRedirectFactory->create()->setPath('sales/order/index');
    }

    /**
     * @param int $orderId
     * @return ResultInterface
     */
    private function redirectToOrder(int $orderId): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($orderId > 0) {
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        return $resultRedirect->setPath('sales/order/index');
    }
}
