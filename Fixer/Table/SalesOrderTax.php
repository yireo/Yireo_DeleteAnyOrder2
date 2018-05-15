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
    /**
     * @var string
     */
    protected $tableName = 'sales_order_tax';

    /**
     * @var string
     */
    protected $orderIdField = 'order_id';
}