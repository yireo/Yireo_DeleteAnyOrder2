<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesPaymentTransaction
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesPaymentTransaction extends AbstractTable
{
    protected string $tableName = 'sales_payment_transaction';
    protected string $orderIdField = 'order_id';
}
