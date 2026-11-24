<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\TestFramework\Helper\Bootstrap;

trait OrderTestTrait
{
    private function getOrderId(string $incrementId = '100000001'): int
    {
        $order = Bootstrap::getObjectManager()->create(Order::class)->loadByIncrementId($incrementId);
        $orderId = (int)$order->getId();
        $this->assertGreaterThan(0, $orderId);

        return $orderId;
    }

    private function orderExists(int $orderId): bool
    {
        $collection = Bootstrap::getObjectManager()->create(OrderCollection::class);
        $collection->addFieldToFilter('entity_id', $orderId);

        return $collection->getSize() > 0;
    }
}
