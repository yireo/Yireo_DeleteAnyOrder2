<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Inventory;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;

class SalabilityUpdater
{
    private const TOPIC = 'inventory.reservations.updateSalabilityStatus';
    private const RESERVATION_DATA_CLASS = 'Magento\InventoryIndexer\Model\Queue\ReservationData';
    private const INDEXER_MODULE = 'Magento_InventoryIndexer';

    /**
     * @param ModuleManager $moduleManager
     * @param ObjectManagerInterface $objectManager
     * @param PublisherInterface $publisher
     */
    public function __construct(
        private readonly ModuleManager $moduleManager,
        private readonly ObjectManagerInterface $objectManager,
        private readonly PublisherInterface $publisher
    ) {
    }

    /**
     * @param array<int, string[]> $skusByStockId
     * @return void
     */
    public function execute(array $skusByStockId): void
    {
        if (empty($skusByStockId) || !$this->isAvailable()) {
            return;
        }

        foreach ($skusByStockId as $stockId => $skus) {
            $reservationData = $this->objectManager->create(self::RESERVATION_DATA_CLASS, [
                'skus' => array_values(array_unique($skus)),
                'stock' => (int)$stockId,
            ]);

            $this->publisher->publish(self::TOPIC, $reservationData);
        }
    }

    /**
     * @return bool
     */
    private function isAvailable(): bool
    {
        return $this->moduleManager->isEnabled(self::INDEXER_MODULE) && class_exists(self::RESERVATION_DATA_CLASS);
    }
}
