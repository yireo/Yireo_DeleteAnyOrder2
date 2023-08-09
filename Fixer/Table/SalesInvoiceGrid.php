<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesInvoiceGrid
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesInvoiceGrid extends AbstractTable
{
    protected string $tableName = 'sales_invoice_grid';
    protected string $orderIdField = 'order_id';
}
