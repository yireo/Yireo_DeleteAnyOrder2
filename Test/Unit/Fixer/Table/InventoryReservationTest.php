<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Unit\Fixer\Table;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;
use Yireo\DeleteAnyOrder2\Fixer\Table\InventoryReservation;
use Yireo\DeleteAnyOrder2\Inventory\SalabilityUpdater;

class InventoryReservationTest extends TestCase
{
    public function testDeleteByInvalidOrderIdDoesNotTouchDatabase(): void
    {
        $resourceConnection = $this->createMock(ResourceConnection::class);
        $resourceConnection->expects($this->never())->method('getConnection');

        $inventoryReservation = new InventoryReservation($resourceConnection, $this->createMock(SalabilityUpdater::class));
        $this->assertSame(0, $inventoryReservation->deleteByOrderId(0));
        $this->assertSame(0, $inventoryReservation->deleteByOrderId(-1));
    }

    public function testDeleteByOrderIdWithoutReservationTableDoesNothing(): void
    {
        $connection = $this->createMock(AdapterInterface::class);
        $connection->expects($this->once())->method('isTableExists')->willReturn(false);
        $connection->expects($this->never())->method('select');
        $connection->expects($this->never())->method('delete');

        $resourceConnection = $this->createMock(ResourceConnection::class);
        $resourceConnection->method('getConnection')->willReturn($connection);
        $resourceConnection->method('getTableName')->willReturnArgument(0);

        $inventoryReservation = new InventoryReservation($resourceConnection, $this->createMock(SalabilityUpdater::class));
        $this->assertFalse($inventoryReservation->isAvailable());
        $this->assertSame(0, $inventoryReservation->deleteByOrderId(42));
    }
}
