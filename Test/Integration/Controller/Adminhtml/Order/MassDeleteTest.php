<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Controller\Adminhtml\Order;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Yireo\DeleteAnyOrder2\Test\Integration\OrderTestTrait;

/**
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends AbstractBackendController
{
    use OrderTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_DeleteAnyOrder2::delete_order';
        $this->uri = 'backend/deleteanyorder/order/massDelete';
        $this->httpMethod = HttpRequest::METHOD_POST;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testGetRequestDoesNotDeleteOrders(): void
    {
        $orderId = $this->getOrderId();

        $this->getRequest()->setMethod(HttpRequest::METHOD_GET);
        $this->getRequest()->setParams(['selected' => [$orderId], 'namespace' => 'sales_order_grid']);
        $this->dispatch($this->uri);

        $this->assertSame(404, $this->getResponse()->getHttpResponseCode());
        $this->assertTrue($this->orderExists($orderId));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testPostRequestDeletesSelectedOrders(): void
    {
        $orderId = $this->getOrderId();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue(['selected' => [$orderId], 'namespace' => 'sales_order_grid']);
        $this->dispatch($this->uri);

        $this->assertRedirect($this->stringContains('sales/order/index'));
        $this->assertFalse($this->orderExists($orderId));
        $this->assertSessionMessages($this->containsEqual('Removed 1 orders'));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testPostRequestWithoutSelectionDeletesNothing(): void
    {
        $orderId = $this->getOrderId();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue(['namespace' => 'sales_order_grid']);
        $this->dispatch($this->uri);

        $this->assertTrue($this->orderExists($orderId));
    }
}
