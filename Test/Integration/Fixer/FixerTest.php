<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Fixer;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderGrid;

/**
 * @magentoDbIsolation enabled
 * @magentoAppArea adminhtml
 */
class FixerTest extends TestCase
{
    private const ORPHAN_ORDER_ID = 987654321;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testOrphansAreCountedAndRemoved(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $connection = $objectManager->get(ResourceConnection::class)->getConnection();
        $gridTable = $connection->getTableName('sales_order_grid');

        $order = $objectManager->create(Order::class)->loadByIncrementId('100000001');
        $orderId = (int)$order->getId();
        $this->assertGreaterThan(0, $orderId);

        $connection->delete($gridTable, ['entity_id IN (?)' => [$orderId, self::ORPHAN_ORDER_ID]]);
        $connection->insertMultiple($gridTable, [
            ['entity_id' => $orderId, 'increment_id' => '100000001'],
            ['entity_id' => self::ORPHAN_ORDER_ID, 'increment_id' => 'orphan'],
        ]);

        $fixer = $objectManager->create(Fixer::class);
        $table = $objectManager->create(SalesOrderGrid::class);

        $this->assertSame(1, $fixer->getOrphansCountPerTable($table));
        $this->assertGreaterThanOrEqual(2, $fixer->getTotalsPerTable($table));

        $fixer->fix(function () {
        });

        $this->assertSame(0, $fixer->getOrphansCountPerTable($table));
        $remainingIds = $connection->fetchCol(
            $connection->select()->from($gridTable, ['entity_id'])->where('entity_id = ?', $orderId)
        );
        $this->assertSame([(string)$orderId], array_map('strval', $remainingIds));
    }
}
