<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use InvalidArgumentException;
use Magento\Framework\App\ResourceConnection;
use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;
use Yireo\DeleteAnyOrder2\Inventory\SalabilityUpdater;

class InventoryReservation extends AbstractTable
{
    private const ORDER_ID_PATTERN = '^[1-9][0-9]*$';

    private const METADATA_KEYS = ['object_id', 'object_type', 'object_increment_id'];

    protected string $tableName = 'inventory_reservation';
    protected string $orderIdField = 'metadata';

    private ?bool $available = null;

    private ?bool $hasIncrementIdColumn = null;

    /**
     * @param ResourceConnection $resourceConnection
     * @param SalabilityUpdater $salabilityUpdater
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        private readonly SalabilityUpdater $salabilityUpdater
    ) {
        parent::__construct($resourceConnection);
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        if ($this->available === null) {
            $this->available = $this->resourceConnection->getConnection()->isTableExists($this->getTableName());
        }

        return $this->available;
    }

    /**
     * @return string
     */
    public function getOrderIdExpression(): string
    {
        return $this->getMetadataExpression('object_id');
    }

    /**
     * @return string[]
     */
    public function getConditions(): array
    {
        $orderId = $this->getOrderIdExpression();
        $incrementId = $this->getIncrementIdExpression();

        return [
            $this->getMetadataExpression('object_type') . " = 'order'",
            "(" . $orderId . " REGEXP '" . self::ORDER_ID_PATTERN . "' OR " . $incrementId . " <> '')",
        ];
    }

    /**
     * @return array
     */
    public function getOrphanConditions(): array
    {
        $orderId = $this->getOrderIdExpression();
        $incrementId = $this->getIncrementIdExpression();

        return [
            '((' . $orderId . " REGEXP '" . self::ORDER_ID_PATTERN . "' AND " . $orderId
            . ' NOT IN (' . $this->getSalesOrderSelect('entity_id')->assemble() . ')) OR ('
            . 'COALESCE(' . $orderId . ", '') NOT REGEXP '" . self::ORDER_ID_PATTERN . "' AND " . $incrementId
            . ' NOT IN (' . $this->getSalesOrderSelect('increment_id')->assemble() . ')))',
        ];
    }

    /**
     * @return string
     */
    public function getPrimaryKeyField(): string
    {
        return 'reservation_id';
    }

    /**
     * @param int $orderId
     * @param string $incrementId
     * @return int
     */
    public function deleteByOrderId(int $orderId, string $incrementId = ''): int
    {
        if ($orderId < 1 || !$this->isAvailable()) {
            return 0;
        }

        $connection = $this->resourceConnection->getConnection();
        $orderIdExpression = $this->getOrderIdExpression();
        $orderCondition = $connection->quoteInto($orderIdExpression . ' = ?', (string)$orderId);
        if ($incrementId !== '') {
            $orderCondition = '(' . $orderCondition . ') OR ('
                . 'COALESCE(' . $orderIdExpression . ", '') NOT REGEXP '" . self::ORDER_ID_PATTERN . "' AND "
                . $connection->quoteInto($this->getIncrementIdExpression() . ' = ?', $incrementId) . ')';
        }

        $select = $connection->select()
            ->from($this->getTableName(), [$this->getPrimaryKeyField()])
            ->where($orderCondition);

        foreach ($this->getConditions() as $condition) {
            $select->where($condition);
        }

        return $this->deleteByPrimaryKeys($connection->fetchCol($select));
    }

    /**
     * @param array $ids
     * @return int
     */
    public function deleteByPrimaryKeys(array $ids): int
    {
        $ids = array_map('intval', $ids);
        if (empty($ids)) {
            return 0;
        }

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->getTableName(), ['stock_id', 'sku'])
            ->where($connection->quoteIdentifier($this->getPrimaryKeyField()) . ' IN (?)', $ids);

        $skusByStockId = [];
        foreach ($connection->fetchAll($select) as $row) {
            $skusByStockId[(int)$row['stock_id']][] = (string)$row['sku'];
        }

        $deleted = parent::deleteByPrimaryKeys($ids);
        $this->salabilityUpdater->execute($skusByStockId);

        return $deleted;
    }

    /**
     * @return string
     */
    private function getIncrementIdExpression(): string
    {
        if ($this->hasIncrementIdColumn === null) {
            $this->hasIncrementIdColumn = $this->resourceConnection->getConnection()->tableColumnExists(
                $this->getTableName(),
                'object_increment_id'
            );
        }

        $metadataExpression = $this->getMetadataExpression('object_increment_id');
        if (!$this->hasIncrementIdColumn) {
            return $metadataExpression;
        }

        $column = $this->resourceConnection->getConnection()->quoteIdentifier('object_increment_id');
        return 'COALESCE(' . $column . ', ' . $metadataExpression . ')';
    }

    /**
     * @param string $key
     * @return string
     * @throws InvalidArgumentException
     */
    private function getMetadataExpression(string $key): string
    {
        if (!in_array($key, self::METADATA_KEYS, true)) {
            throw new InvalidArgumentException('Unsupported metadata key: ' . $key);
        }

        return "(CASE WHEN JSON_VALID(metadata) THEN JSON_UNQUOTE(JSON_EXTRACT(metadata, '$." . $key . "')) END)";
    }
}
