<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesInvoice
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesInvoice extends AbstractTable
{
    protected string $tableName = 'sales_invoice';
    protected string $orderIdField = 'order_id';
}
