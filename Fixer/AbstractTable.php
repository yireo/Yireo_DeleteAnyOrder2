<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

/**
 * Class AbstractTable
 *
 * @package Yireo\DeleteAnyOrder2\Fixer
 */
abstract class AbstractTable
{
    protected string $tableName = '';
    protected string $orderIdField = '';

    protected ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return string
     */
    public function getOrderIdField(): string
    {
        return $this->orderIdField;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->resourceConnection->getTableName($this->tableName);
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getOrderIdExpression(): string
    {
        return $this->resourceConnection->getConnection()->quoteIdentifier($this->getOrderIdField());
    }

    /**
     * @return string[]
     */
    public function getConditions(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getOrphanConditions(): array
    {
        return [$this->getOrderIdExpression() . ' NOT IN (?)' => $this->getSalesOrderSelect('entity_id')];
    }

    /**
     * @return string
     */
    public function getPrimaryKeyField(): string
    {
        return '';
    }

    /**
     * @param array $ids
     * @return int
     */
    public function deleteByPrimaryKeys(array $ids): int
    {
        $primaryKey = $this->getPrimaryKeyField();
        if ($primaryKey === '' || empty($ids)) {
            return 0;
        }

        $connection = $this->resourceConnection->getConnection();
        return $connection->delete(
            $this->getTableName(),
            [$connection->quoteIdentifier($primaryKey) . ' IN (?)' => $ids]
        );
    }

    /**
     * @param string $field
     * @return Select
     */
    protected function getSalesOrderSelect(string $field): Select
    {
        return $this->resourceConnection->getConnection()->select()
            ->from($this->resourceConnection->getTableName('sales_order'), [$field]);
    }
}
