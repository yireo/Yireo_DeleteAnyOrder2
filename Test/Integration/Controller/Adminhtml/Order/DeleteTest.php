<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Controller\Adminhtml\Order;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Yireo\DeleteAnyOrder2\Test\Integration\OrderTestTrait;

/**
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    use OrderTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_DeleteAnyOrder2::delete_order';
        $this->uri = 'backend/deleteanyorder/order/delete';
        $this->httpMethod = HttpRequest::METHOD_POST;
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testGetRequestDoesNotDeleteOrder(): void
    {
        $orderId = $this->getOrderId();

        $this->getRequest()->setMethod(HttpRequest::METHOD_GET);
        $this->dispatch($this->uri . '/order_id/' . $orderId);

        $this->assertSame(404, $this->getResponse()->getHttpResponseCode());
        $this->assertTrue($this->orderExists($orderId));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testPostRequestWithoutValidFormKeyDoesNotDeleteOrder(): void
    {
        $orderId = $this->getOrderId();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue('form_key', 'invalid');
        $this->dispatch($this->uri . '/order_id/' . $orderId);

        $this->assertTrue($this->orderExists($orderId));
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testPostRequestDeletesOrder(): void
    {
        $orderId = $this->getOrderId();

        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch($this->uri . '/order_id/' . $orderId);

        $this->assertRedirect($this->stringContains('sales/order/index'));
        $this->assertFalse($this->orderExists($orderId));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testPostRequestWithUnknownOrderShowsError(): void
    {
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->dispatch($this->uri . '/order_id/987654321');

        $this->assertRedirect($this->stringContains('sales/order/view'));
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), 'error');
    }
}
