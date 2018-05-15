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
    /**
     * @var string
     */
    protected $tableName = 'sales_order_item';

    /**
     * @var string
     */
    protected $orderIdField = 'order_id';
}