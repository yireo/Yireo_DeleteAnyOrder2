<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

/**
 * Class AbstractTable
 *
 * @package Yireo\DeleteAnyOrder2\Fixer
 */
abstract class AbstractTable
{
    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string
     */
    protected $orderIdField = '';

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
        return $this->tableName;
    }
}
