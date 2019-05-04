<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

use Yireo\DeleteAnyOrder2\Fixer\DataProvider\OrderId as OrderIdProvider;
use Yireo\DeleteAnyOrder2\Fixer\DataProvider\Table as TableProvider;

use Zend\Db\Exception\RuntimeException;

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

        $tableName = $table->getTableName();
        $orderIdField = $table->getOrderIdField();
        $query = 'SELECT COUNT(`%s`) FROM `%s` WHERE `%s` NOT IN (SELECT `entity_id` FROM `sales_order`)';
        $sql = sprintf($query, $orderIdField, $tableName, $orderIdField);

        return (int)$this->getConnection()->fetchOne($sql);
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getTotalsPerTable(AbstractTable $table): int
    {
        $tableName = $table->getTableName();
        $orderIdField = $table->getOrderIdField();
        $query = 'SELECT COUNT(`%s`) FROM `%s`';
        $sql = sprintf($query, $orderIdField, $tableName);

        return (int)$this->getConnection()->fetchOne($sql);
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

        $orderIds = $this->getCurrentOrderIds();
        $hasOrderIds = (empty($orderIds)) ? false : true;
        $this->loggerCallback = $loggerCallback;

        foreach ($this->getTables() as $table) {
            $tableName = $table->getTableName();
            $orderIdField = $table->getOrderIdField();

            if ($hasOrderIds) {
                $query = 'DELETE FROM `%s` WHERE `%s`';
            } else {
                $query = 'DELETE FROM `%s` WHERE `%s` NOT IN (SELECT `entity_id` FROM `sales_order`)';
            }

            $sql = sprintf($query, $tableName, $orderIdField, implode(',', $orderIds));

            $this->getConnection()->query($sql);

            if ($this->verbosity > 0) {
                $this->log($sql);
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
