<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesOrderPayment
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesOrderPayment extends AbstractTable
{
    protected string $tableName = 'sales_order_payment';
    protected string $orderIdField = 'parent_id';
}
