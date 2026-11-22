<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Yireo\DeleteAnyOrder2\Fixer\Table\InventoryReservation;
use Yireo\DeleteAnyOrder2\Observer\Order\Delete\Cleanup;

trait InventoryReservationFixture
{
    private string $reservationSku = 'deleteanyorder2-reservation-test';

    private static bool $reservationTableCreated = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection = $resourceConnection->getConnection();
        $tableName = $resourceConnection->getTableName('inventory_reservation');
        if ($connection->isTableExists($tableName)) {
            return;
        }

        $table = $connection->newTable($tableName)
            ->addColumn('reservation_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ])
            ->addColumn('stock_id', Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false])
            ->addColumn('sku', Table::TYPE_TEXT, 64, ['nullable' => false])
            ->addColumn('quantity', Table::TYPE_DECIMAL, '10,4', ['nullable' => false, 'default' => '0'])
            ->addColumn('metadata', Table::TYPE_TEXT, 255, ['nullable' => true]);
        $connection->createTable($table);
        self::$reservationTableCreated = true;
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$reservationTableCreated) {
            $resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
            $resourceConnection->getConnection()->dropTable($resourceConnection->getTableName('inventory_reservation'));
            self::$reservationTableCreated = false;
        }

        self::resetInventoryReservationTable();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::resetInventoryReservationTable();
    }

    private static function resetInventoryReservationTable(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        if ($objectManager instanceof ObjectManager) {
            $objectManager->removeSharedInstance(InventoryReservation::class);
            $objectManager->removeSharedInstance(Cleanup::class);
        }
    }

    private function getConnection(): AdapterInterface
    {
        return Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();
    }

    private function getReservationTable(): string
    {
        return Bootstrap::getObjectManager()->get(ResourceConnection::class)->getTableName('inventory_reservation');
    }

    private function addReservation(?string $metadata, float $quantity = -1): void
    {
        $this->getConnection()->insert($this->getReservationTable(), [
            'stock_id' => 1,
            'sku' => $this->reservationSku,
            'quantity' => $quantity,
            'metadata' => $metadata,
        ]);
    }

    private function getOrderMetadata(
        int|string $orderId,
        string $incrementId = '',
        string $eventType = 'order_placed',
        string $objectType = 'order'
    ): string {
        return (string)json_encode([
            'event_type' => $eventType,
            'object_type' => $objectType,
            'object_id' => $orderId,
            'object_increment_id' => $incrementId,
        ]);
    }

    /**
     * @return array<int, string|null>
     */
    private function getReservationMetadata(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getReservationTable(), ['metadata'])
            ->where('sku = ?', $this->reservationSku)
            ->order('reservation_id ASC');

        return $connection->fetchCol($select);
    }
}
