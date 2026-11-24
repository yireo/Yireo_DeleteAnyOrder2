<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Yireo\DeleteAnyOrder2\Order\OrderDeleter;

class MassDelete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::delete_order';

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param OrderDeleter $orderDeleter
     */
    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly OrderDeleter $orderDeleter
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/index');

        try {
            $orderIds = $this->filter->getCollection($this->collectionFactory->create())->getAllIds();
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $resultRedirect;
        }

        $deleted = 0;
        $failed = 0;
        foreach ($orderIds as $orderId) {
            try {
                $this->orderDeleter->delete((int)$orderId);
                $deleted++;
            } catch (Exception $exception) {
                $failed++;
            }
        }

        $this->messageManager->addNoticeMessage(__('Removed %1 orders', $deleted));
        if ($failed > 0) {
            $this->messageManager->addErrorMessage(__('Unable to remove %1 orders', $failed));
        }

        return $resultRedirect;
    }
}
