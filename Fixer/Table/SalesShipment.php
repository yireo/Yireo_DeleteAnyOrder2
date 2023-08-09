<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesShipment
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesShipment extends AbstractTable
{
    protected string $tableName = 'sales_shipment';
    protected string $orderIdField = 'order_id';
}
