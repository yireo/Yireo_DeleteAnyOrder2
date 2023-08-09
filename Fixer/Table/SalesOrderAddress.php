<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesOrderAddress
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesOrderAddress extends AbstractTable
{
    protected string $tableName = 'sales_order_address';
    protected string $orderIdField = 'parent_id';
}
