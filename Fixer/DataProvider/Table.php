<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\DataProvider;

use Yireo\DeleteAnyOrder2\Fixer\Table\SalesCreditmemo;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesInvoice;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesInvoiceGrid;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderAddress;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderGrid;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderItem;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderPayment;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderStatusHistory;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesOrderTax;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesPaymentTransaction;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesShipment;
use Yireo\DeleteAnyOrder2\Fixer\Table\SalesShipmentGrid;

/**
 * Class Tables
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\DataProvider
 */
class Table
{
    /**
     * @return array
     */
    public function getTableClasses(): array
    {
        return [
            SalesCreditmemo::class,
            SalesInvoice::class,
            SalesInvoiceGrid::class,
            SalesOrderAddress::class,
            SalesOrderGrid::class,
            SalesOrderItem::class,
            SalesOrderPayment::class,
            SalesOrderStatusHistory::class,
            SalesOrderTax::class,
            SalesPaymentTransaction::class,
            SalesShipment::class,
            SalesShipmentGrid::class,
        ];
    }
}