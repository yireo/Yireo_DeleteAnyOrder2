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
        ResourceConnection\Proxy $resourceConnection,
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
            $orphans = $this->getOrphansPerTable($table);
            $tableName = $table->getTableName();
            $this->log(sprintf('Found %d order orphans in %s', count($orphans), $tableName));
        }
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getOrphansPerTable(AbstractTable $table)
    {
        $tableName = $table->getTableName();
        $orderIdField = $table->getOrderIdField();
        $query = 'SELECT `%s` FROM `%s` WHERE `%s` NOT IN (%s)';
        $sql = sprintf($query, $orderIdField, $tableName, $orderIdField, implode(',', $this->getCurrentOrderIds()));

        return count($this->getConnection()->fetchAll($sql));
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getTotalsPerTable(AbstractTable $table)
    {
        $tableName = $table->getTableName();
        $orderIdField = $table->getOrderIdField();
        $query = 'SELECT `%s` FROM `%s`';
        $sql = sprintf($query, $orderIdField, $tableName);

        return count($this->getConnection()->fetchAll($sql));
    }

    /**
     * @param callable $loggerCallback
     */
    public function fix(callable $loggerCallback)
    {
        $this->init($loggerCallback);
        $this->loggerCallback = $loggerCallback;

        foreach ($this->getTables() as $table) {
            $tableName = $table->getTableName();
            $orderIdField = $table->getOrderIdField();
            $query = 'DELETE FROM `%s` WHERE `%s` NOT IN (%s)';
            $sql = sprintf($query, $tableName, $orderIdField, implode(',', $this->getCurrentOrderIds()));

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
