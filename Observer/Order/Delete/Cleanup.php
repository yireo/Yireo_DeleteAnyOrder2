<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Observer\Order\Delete;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Yireo\DeleteAnyOrder2\Fixer\Table\InventoryReservation;

class Cleanup implements ObserverInterface
{
    /**
     * @param InventoryReservation $inventoryReservation
     */
    public function __construct(
        private readonly InventoryReservation $inventoryReservation
    ) {
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $order = $observer->getEvent()->getData('order');
        if (!$order instanceof OrderInterface) {
            return;
        }

        $this->inventoryReservation->deleteByOrderId(
            (int)$order->getEntityId(),
            (string)$order->getIncrementId()
        );
    }
}
