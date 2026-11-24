<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Order;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Yireo\DeleteAnyOrder2\Logger\AuditLogger;

class OrderDeleter
{
    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param AuditLogger $auditLogger
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly AuditLogger $auditLogger
    ) {
    }

    /**
     * @param int $orderId
     * @param string|null $actor
     * @return void
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function delete(int $orderId, ?string $actor = null): void
    {
        if ($orderId < 1) {
            throw new NoSuchEntityException(__('Invalid order ID'));
        }

        $order = $this->orderRepository->get($orderId);
        $incrementId = (string)$order->getIncrementId();
        $this->orderRepository->delete($order);

        $this->auditLogger->log('Deleted order', [
            'order_id' => $orderId,
            'increment_id' => $incrementId,
        ], $actor);
    }
}
