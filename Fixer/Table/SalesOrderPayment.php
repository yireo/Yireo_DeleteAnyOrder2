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
    /**
     * @var string
     */
    protected $tableName = 'sales_order_payment';

    /**
     * @var string
     */
    protected $orderIdField = 'parent_id';
}
