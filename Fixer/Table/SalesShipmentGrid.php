<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesShipmentGrid
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesShipmentGrid extends AbstractTable
{
    protected string $tableName = 'sales_shipment_grid';
    protected string $orderIdField = 'order_id';
}
