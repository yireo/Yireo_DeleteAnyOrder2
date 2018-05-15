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
    /**
     * @var string
     */
    protected $tableName = 'sales_order_address';

    /**
     * @var string
     */
    protected $orderIdField = 'parent_id';
}