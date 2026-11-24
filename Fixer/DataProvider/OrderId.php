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
     * @return bool
     */
    public function hasOrders(): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('sales_order'), ['entity_id'])
            ->limit(1);

        return (bool)$connection->fetchOne($select);
    }
}
