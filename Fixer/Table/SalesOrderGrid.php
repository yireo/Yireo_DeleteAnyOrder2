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
    /**
     * @var string
     */
    protected $tableName = 'sales_order_grid';

    /**
     * @var string
     */
    protected $orderIdField = 'entity_id';
}