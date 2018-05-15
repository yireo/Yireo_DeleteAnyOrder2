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
    /**
     * @var string
     */
    protected $tableName = 'sales_shipment_grid';

    /**
     * @var string
     */
    protected $orderIdField = 'order_id';
}