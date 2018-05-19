<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Delete
 *
 * @package Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order
 */
class Delete extends Action
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
        $orderId = (int)$this->_request->getParam('order_id');
        if ($this->deleteByOrderId($orderId)) {
            $this->messageManager->addNoticeMessage('Removed order');
            $redirectPath = 'sales/order/index';
        } else {
            $this->messageManager->addNoticeMessage('Unable to removed order');
            $redirectPath = 'sales/order/view/order_id/'.$orderId;
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($redirectPath);

        return $resultRedirect;
    }

    /**
     * @param int $orderId
     *
     * @return bool
     */
    private function deleteByOrderId(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);
        $this->orderRepository->delete($order);

        return true;
    }
}
