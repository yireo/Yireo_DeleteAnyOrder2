<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use RuntimeException;
use Yireo\DeleteAnyOrder2\Fixer\DataProvider\OrderId as OrderIdProvider;
use Yireo\DeleteAnyOrder2\Fixer\DataProvider\Table as TableProvider;

/**
 * Class Fixer
 *
 * @package Yireo\DeleteAnyOrder2\Fixer
 */
class Fixer
{
    /**
     * @var callable
     */
    private $loggerCallback;

    /**
     * @var OrderIdProvider
     */
    private $orderIdProvider;

    /**
     * @var TableProvider
     */
    private $tableProvider;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var TableFactory
     */
    private $tableFactory;

    /**
     * @var int
     */
    private $verbosity = 0;

    /**
     * @var int[]
     */
    private $currentOrderIds = [];

    /**
     * Fixer constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param OrderIdProvider $orderIdProvider
     * @param TableProvider $tableProvider
     * @param TableFactory $tableFactory
     * @param int $verbosity
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        OrderIdProvider $orderIdProvider,
        TableProvider $tableProvider,
        TableFactory $tableFactory,
        $verbosity = 0
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->orderIdProvider = $orderIdProvider;
        $this->tableProvider = $tableProvider;
        $this->tableFactory = $tableFactory;
        $this->verbosity = $verbosity;
    }

    /**
     * @return int[]
     */
    private function getCurrentOrderIds(): array
    {
        if (empty($this->currentOrderIds)) {
            $this->currentOrderIds = $this->orderIdProvider->getData();
        }

        return $this->currentOrderIds;
    }

    /**
     * @return AdapterInterface
     */
    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }

    /**
     * @param callable $loggerCallback
     */
    private function init(callable $loggerCallback)
    {
        $this->loggerCallback = $loggerCallback;
        $currentOrderIds = $this->getCurrentOrderIds();

        $this->log('Found %d valid orders', count($currentOrderIds));

        if (empty($currentOrderIds)) {
            throw new RuntimeException('No orders to clean up');
        }
    }

    /**
     * @param callable $loggerCallback
     */
    public function analyse(callable $loggerCallback)
    {
        $this->loggerCallback = $loggerCallback;

        foreach ($this->getTables() as $table) {
            $orphans = $this->getOrphansCountPerTable($table);
            $tableName = $table->getTableName();
            $this->log(sprintf('Found %d order orphans in %s', $orphans, $tableName));
        }
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getOrphansCountPerTable(AbstractTable $table): int
    {
        $currentOrderIds = $this->getCurrentOrderIds();
        if (empty($currentOrderIds)) {
            return 0;
        }

        $orderIdField = $this->getConnection()->quoteIdentifier($table->getOrderIdField());
        $select = $this->getConnection()->select()
            ->from($table->getTableName(), ['COUNT(' . $orderIdField . ')'])
            ->where($orderIdField . ' NOT IN (?)', $this->getSalesOrderIdsSelect());

        return (int)$this->getConnection()->fetchOne($select);
    }

    private function getSalesOrderIdsSelect(): Select
    {
        return $this->getConnection()->select()
            ->from($this->getConnection()->getTableName('sales_order'), ['entity_id']);
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getTotalsPerTable(AbstractTable $table): int
    {
        $orderIdField = $this->getConnection()->quoteIdentifier($table->getOrderIdField());
        $select = $this->getConnection()->select()
            ->from($table->getTableName(), ['COUNT(' . $orderIdField . ')']);

        return (int)$this->getConnection()->fetchOne($select);
    }

    /**
     * @param callable $loggerCallback
     */
    public function fix(callable $loggerCallback)
    {
        try {
            $this->init($loggerCallback);
        } catch (RuntimeException $e) {
            return;
        }

        $this->loggerCallback = $loggerCallback;

        foreach ($this->getTables() as $table) {
            $tableName = $table->getTableName();
            $orderIdField = $this->getConnection()->quoteIdentifier($table->getOrderIdField());
            $where = [$orderIdField . ' NOT IN (?)' => $this->getSalesOrderIdsSelect()];
            $deleted = $this->getConnection()->delete($tableName, $where);

            if ($this->verbosity > 0) {
                $this->log(sprintf('Deleted %d rows from %s', $deleted, $tableName));
            }

            $this->log(sprintf('Removed all orders orphans in %s', $tableName));
        }
    }

    /**
     * @return AbstractTable[]
     */
    public function getTables()
    {
        $tables = [];
        $tableClasses = $this->tableProvider->getTableClasses();
        foreach ($tableClasses as $tableClass) {
            $tables[] = $this->tableFactory->create($tableClass);
        }

        return $tables;
    }

    /**
     * @param string $message
     * @param mixed ...$parameters
     */
    private function log(string $message, ...$parameters)
    {
        call_user_func($this->loggerCallback, sprintf($message, ...$parameters));
    }
}
