<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesOrderTax
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesOrderTax extends AbstractTable
{
    protected string $tableName = 'sales_order_tax';
    protected string $orderIdField = 'order_id';
}
