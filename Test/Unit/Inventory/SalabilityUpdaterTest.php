<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Unit\Inventory;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yireo\DeleteAnyOrder2\Inventory\SalabilityUpdater;

class SalabilityUpdaterTest extends TestCase
{
    private const RESERVATION_DATA_CLASS = 'Magento\InventoryIndexer\Model\Queue\ReservationData';

    public function testPublishesSalabilityUpdatePerStock(): void
    {
        if (!class_exists(self::RESERVATION_DATA_CLASS)) {
            $this->markTestSkipped('MSI is not installed');
        }

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->exactly(2))->method('create')
            ->willReturnCallback(function (string $class, array $arguments) {
                $this->assertSame(self::RESERVATION_DATA_CLASS, $class);
                $data = new stdClass();
                $data->arguments = $arguments;
                return $data;
            });

        $published = [];
        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->method('publish')->willReturnCallback(function (string $topic, $data) use (&$published) {
            $published[] = [$topic, $data->arguments];
            return null;
        });

        $this->createSalabilityUpdater(true, $objectManager, $publisher)->execute([
            1 => ['sku-a', 'sku-b', 'sku-a'],
            2 => ['sku-c'],
        ]);

        $this->assertSame([
            ['inventory.reservations.updateSalabilityStatus', ['skus' => ['sku-a', 'sku-b'], 'stock' => 1]],
            ['inventory.reservations.updateSalabilityStatus', ['skus' => ['sku-c'], 'stock' => 2]],
        ], $published);
    }

    public function testDoesNothingWithoutInventoryIndexer(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects($this->never())->method('create');
        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->expects($this->never())->method('publish');

        $this->createSalabilityUpdater(false, $objectManager, $publisher)->execute([1 => ['sku-a']]);
    }

    private function createSalabilityUpdater(
        bool $moduleEnabled,
        ObjectManagerInterface $objectManager,
        PublisherInterface $publisher
    ): SalabilityUpdater {
        $moduleManager = $this->createMock(ModuleManager::class);
        $moduleManager->method('isEnabled')->with('Magento_InventoryIndexer')->willReturn($moduleEnabled);

        return new SalabilityUpdater($moduleManager, $objectManager, $publisher);
    }
}
