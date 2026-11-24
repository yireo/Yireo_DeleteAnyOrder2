<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Sql\Expression;

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
        return $this->resourceConnection->getConnection()->quoteIdentifier(
            [$this->getTableName(), $this->getOrderIdField()]
        );
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
        $connection = $this->resourceConnection->getConnection();
        $orderIdExpression = $this->getOrderIdExpression();
        $salesOrderSelect = $connection->select()
            ->from(['orphan_check' => $this->resourceConnection->getTableName('sales_order')], [new Expression('1')])
            ->where($connection->quoteIdentifier(['orphan_check', 'entity_id']) . ' = ' . $orderIdExpression);

        return [
            $orderIdExpression . ' IS NOT NULL',
            'NOT EXISTS (' . $salesOrderSelect->assemble() . ')',
        ];
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
