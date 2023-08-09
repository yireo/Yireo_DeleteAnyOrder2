<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesOrderGrid
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesOrderGrid extends AbstractTable
{
    protected string $tableName = 'sales_order_grid';
    protected string $orderIdField = 'entity_id';
}
