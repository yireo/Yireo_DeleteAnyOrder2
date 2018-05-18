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
     * @var int[]
     */
    private $currentOrderIds = [];

    /**
     * @var AdapterInterface
     */
    private $connection;

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
     * Fixer constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param OrderIdProvider $orderIdProvider
     * @param TableProvider $tableProvider
     * @param TableFactory $tableFactory
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        OrderIdProvider $orderIdProvider,
        TableProvider $tableProvider,
        TableFactory $tableFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->orderIdProvider = $orderIdProvider;
        $this->tableProvider = $tableProvider;
        $this->tableFactory = $tableFactory;
    }

    /**
     * @param callable $loggerCallback
     */
    private function init(callable $loggerCallback)
    {
        $this->loggerCallback = $loggerCallback;

        $this->currentOrderIds = $this->orderIdProvider->getData();
        $this->log('Found %d valid orders', count($this->currentOrderIds));

        if (empty($this->currentOrderIds)) {
            throw new RuntimeException('No orders to clean up');
        }

        $this->connection = $this->resourceConnection->getConnection();
    }

    /**
     * @param callable $loggerCallback
     */
    public function analyse(callable $loggerCallback)
    {
        $this->init($loggerCallback);
        $this->loggerCallback = $loggerCallback;

        foreach ($this->getTables() as $table) {
            $tableName = $table->getTableName();
            $orderIdField = $table->getOrderIdField();
            $query = 'SELECT `%s` FROM `%s` WHERE `%s` NOT IN (%s)';
            $sql = sprintf($query, $orderIdField, $tableName, $orderIdField, implode(',', $this->currentOrderIds));

            $results = $this->connection->fetchAll($sql);
            $this->log(sprintf('Found %d order orphans in %s', count($results), $tableName));
        }
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
            $sql = sprintf($query, $tableName, $orderIdField, implode(',', $this->currentOrderIds));

            $this->connection->query($sql);
            $this->log($sql);
            $this->log(sprintf('Removed all orders orphans in %s', $tableName));
        }
    }

    /**
     * @return AbstractTable[]
     */
    private function getTables(): array
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
