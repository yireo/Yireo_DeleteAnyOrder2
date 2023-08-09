<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer\Table;

use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class SalesCreditmemo
 *
 * @package Yireo\DeleteAnyOrder2\Fixer\Table
 */
class SalesCreditmemo extends AbstractTable
{
    protected string $tableName = 'sales_creditmemo';
    protected string $orderIdField = 'order_id';
}
