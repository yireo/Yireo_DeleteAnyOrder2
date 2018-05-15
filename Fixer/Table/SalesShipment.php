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
    /**
     * @var string
     */
    protected $tableName = 'sales_shipment';

    /**
     * @var string
     */
    protected $orderIdField = 'order_id';
}