<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesOrderStatusHistory
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesOrderStatusHistory extends AbstractTable
{
    protected string $tableName = 'sales_order_status_history';
    protected string $orderIdField = 'parent_id';
}
