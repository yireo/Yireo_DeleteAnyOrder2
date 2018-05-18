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
    /**
     * @var string
     */
    protected $tableName = 'sales_payment_transaction';

    /**
     * @var string
     */
    protected $orderIdField = 'order_id';
}
