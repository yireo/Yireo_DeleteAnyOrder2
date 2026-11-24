<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Observer\Order\Delete;

use Magento\Framework\Registry;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\DeleteAnyOrder2\Test\Integration\InventoryReservationFixture;

/**
 * @magentoDbIsolation enabled
 * @magentoAppArea adminhtml
 */
class CleanupTest extends TestCase
{
    use InventoryReservationFixture;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testDeletingOrderOnlyDeletesItsOwnReservations(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $order = $objectManager->create(Order::class)->loadByIncrementId('100000001');
        $orderId = (int)$order->getId();
        $this->assertGreaterThan(0, $orderId);

        $this->addReservation($this->getOrderMetadata('', '100000001'));
        $this->addReservation($this->getOrderMetadata((string)$orderId, '100000001', 'shipment_created'), 1);
        $this->addReservation($this->getOrderMetadata($orderId, '', 'order_canceled'), 1);

        $otherReservations = [
            $this->getOrderMetadata((string)($orderId * 10 + 3)),
            $this->getOrderMetadata('1' . $orderId),
            $this->getOrderMetadata('', '1100000001'),
            $this->getOrderMetadata('', '10000000'),
            $this->getOrderMetadata((string)($orderId + 1000), '100000001'),
            $this->getOrderMetadata((string)$orderId, '100000001', 'order_placed', 'quote'),
            $this->getOrderMetadata(''),
            'not-json',
            null,
        ];
        foreach ($otherReservations as $metadata) {
            $this->addReservation($metadata);
        }

        $registry = $objectManager->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $orderRepository = $objectManager->get(OrderRepositoryInterface::class);
        $orderRepository->delete($orderRepository->get($orderId));

        $registry->unregister('isSecureArea');

        $this->assertSame($otherReservations, $this->getReservationMetadata());
    }
}
