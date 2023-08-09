<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesOrderItem
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesOrderItem extends AbstractTable
{
    protected string $tableName = 'sales_order_item';
    protected string $orderIdField = 'order_id';
}
