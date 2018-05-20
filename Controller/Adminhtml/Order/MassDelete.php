<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class MassDelete
 *
 * @package Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order
 */
class MassDelete extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::index';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * MassReindex constructor.
     *
     * @param Context $context
     * @param ResultRedirectFactory $resultRedirectFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        ResultRedirectFactory $resultRedirectFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $orderIds = $this->getOrderIds();
        if ($this->deleteByOrderIds($orderIds)) {
            $count = count($orderIds);
            $this->messageManager->addNoticeMessage(sprintf('Removed %d orders', $count));
        } else {
            $this->messageManager->addNoticeMessage('Removed 0 orders');
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/order/index');
        return $resultRedirect;
    }

    /**
     * @return array
     */
    private function getOrderIds(): array
    {
        $selected = $this->_request->getParam('selected');
        $excluded = $this->_request->getParam('excluded');

        $orderIds = $selected;
        return (array) $orderIds;
    }

    /**
     * @param array $orderIds
     *
     * @return bool
     */
    private function deleteByOrderIds(array $orderIds): bool
    {
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            $this->orderRepository->delete($order);
        }

        return true;
    }
}
