<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\DataProvider;

use Magento\Framework\App\ResourceConnection;

/**
 * Class OrderId
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\DataProvider
 */
class OrderId
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * OrderId constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $salesOrderTable = $this->resourceConnection->getTableName('sales_order');
        $sql = 'SELECT `entity_id` FROM `'.$salesOrderTable.'`';
        $query = $this->resourceConnection->getConnection()->query($sql);

        $orderIds = [];
        while ($row = $query->fetch()) {
            $orderIds[] = $row['entity_id'];
        }

        return $orderIds;
    }
}
