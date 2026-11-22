<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Fixer;

use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;
use Yireo\DeleteAnyOrder2\Fixer\Table\InventoryReservation;
use Yireo\DeleteAnyOrder2\Test\Integration\InventoryReservationFixture;

/**
 * @magentoDbIsolation enabled
 * @magentoAppArea adminhtml
 */
class InventoryReservationFixerTest extends TestCase
{
    use InventoryReservationFixture;

    private const ORPHAN_ORDER_ID = 987654321;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testOrphanedReservationsAreCountedAndRemoved(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $orderId = (int)$objectManager->create(Order::class)->loadByIncrementId('100000001')->getId();
        $this->assertGreaterThan(0, $orderId);

        $fixer = $objectManager->create(Fixer::class);
        $table = $objectManager->get(InventoryReservation::class);
        $this->assertContains($table, $fixer->getTables());

        $orphansBefore = $fixer->getOrphansCountPerTable($table);
        $totalsBefore = $fixer->getTotalsPerTable($table);

        $this->addReservation($this->getOrderMetadata((string)self::ORPHAN_ORDER_ID));
        $this->addReservation($this->getOrderMetadata(self::ORPHAN_ORDER_ID, '', 'order_canceled'), 1);
        $this->addReservation($this->getOrderMetadata('', 'orphan-increment-id'));

        $remainingReservations = [
            $this->getOrderMetadata((string)$orderId),
            $this->getOrderMetadata('', '100000001'),
            $this->getOrderMetadata((string)self::ORPHAN_ORDER_ID, '', 'order_placed', 'quote'),
            $this->getOrderMetadata(''),
            'not-json',
            null,
        ];
        foreach ($remainingReservations as $metadata) {
            $this->addReservation($metadata);
        }

        $this->assertSame($orphansBefore + 3, $fixer->getOrphansCountPerTable($table));
        $this->assertSame($totalsBefore + 5, $fixer->getTotalsPerTable($table));

        $messages = [];
        $fixer->fix(function (string $message) use (&$messages) {
            $messages[] = $message;
        });

        $this->assertSame(0, $fixer->getOrphansCountPerTable($table));
        $this->assertSame($remainingReservations, $this->getReservationMetadata());
        $this->assertContains('Removed all orders orphans in ' . $table->getTableName(), $messages);
    }
}
