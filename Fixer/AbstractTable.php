<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

use Magento\Framework\App\ResourceConnection;

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
}
