<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Controller\Adminhtml\Index;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\ResourceConnection;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class FixTest extends AbstractBackendController
{
    private const ORPHAN_ORDER_ID = 987654321;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_DeleteAnyOrder2::fix';
        $this->uri = 'backend/deleteanyorder/index/fix';
        $this->httpMethod = HttpRequest::METHOD_POST;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testGetRequestDoesNotFixDatabase(): void
    {
        $this->addOrphan();

        $this->getRequest()->setMethod(HttpRequest::METHOD_GET);
        $this->dispatch($this->uri);

        $this->assertSame(404, $this->getResponse()->getHttpResponseCode());
        $this->assertTrue($this->orphanExists());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testPostRequestFixesDatabase(): void
    {
        $this->addOrphan();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch($this->uri);

        $this->assertRedirect($this->stringContains('deleteanyorder/index/index'));
        $this->assertFalse($this->orphanExists());
    }

    private function addOrphan(): void
    {
        $resourceConnection = $this->_objectManager->get(ResourceConnection::class);
        $gridTable = $resourceConnection->getTableName('sales_order_grid');
        $connection = $resourceConnection->getConnection();
        $connection->delete($gridTable, ['entity_id = ?' => self::ORPHAN_ORDER_ID]);
        $connection->insert($gridTable, ['entity_id' => self::ORPHAN_ORDER_ID, 'increment_id' => 'orphan']);
    }

    private function orphanExists(): bool
    {
        $resourceConnection = $this->_objectManager->get(ResourceConnection::class);
        $connection = $resourceConnection->getConnection();
        $select = $connection->select()
            ->from($resourceConnection->getTableName('sales_order_grid'), ['entity_id'])
            ->where('entity_id = ?', self::ORPHAN_ORDER_ID);

        return (bool)$connection->fetchOne($select);
    }
}
