<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Unit\Observer\Order\Delete;

use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use PHPUnit\Framework\TestCase;
use Yireo\DeleteAnyOrder2\Fixer\Table\InventoryReservation;
use Yireo\DeleteAnyOrder2\Observer\Order\Delete\Cleanup;

class CleanupTest extends TestCase
{
    public function testDeletesReservationsOfDeletedOrder(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getEntityId')->willReturn('42');
        $order->method('getIncrementId')->willReturn('100000042');

        $inventoryReservation = $this->createMock(InventoryReservation::class);
        $inventoryReservation->expects($this->once())->method('deleteByOrderId')->with(42, '100000042');

        (new Cleanup($inventoryReservation))->execute($this->createObserver($order));
    }

    public function testSkipsEventWithoutOrder(): void
    {
        $inventoryReservation = $this->createMock(InventoryReservation::class);
        $inventoryReservation->expects($this->never())->method('deleteByOrderId');

        (new Cleanup($inventoryReservation))->execute($this->createObserver(new DataObject()));
    }

    private function createObserver(mixed $order): Observer
    {
        $event = new Event(['order' => $order]);
        return new Observer(['event' => $event]);
    }
}
