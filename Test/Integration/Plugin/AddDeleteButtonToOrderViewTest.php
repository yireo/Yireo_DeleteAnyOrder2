<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Plugin;

use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Yireo\DeleteAnyOrder2\Test\Integration\OrderTestTrait;

/**
 * @magentoAppArea adminhtml
 */
class AddDeleteButtonToOrderViewTest extends AbstractBackendController
{
    use OrderTestTrait;

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testOrderViewContainsPostDeleteButton(): void
    {
        $this->dispatch('backend/sales/order/view/order_id/' . $this->getOrderId());
        $body = (string)$this->getResponse()->getBody();

        $this->assertStringContainsString('id="deleteanyorder"', $body);
        $this->assertStringContainsString('deleteConfirm(', $body);
        $this->assertStringContainsString('{data: {}}', $body);
        $this->assertStringNotContainsString('confirmSetLocation(', $body);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDbIsolation enabled
     */
    public function testOrderViewHidesDeleteButtonWithoutPermission(): void
    {
        $acl = $this->_objectManager->get(AclBuilder::class)->getAcl();
        $acl->deny($this->_auth->getUser()->getRoles(), 'Yireo_DeleteAnyOrder2::delete_order');

        $this->dispatch('backend/sales/order/view/order_id/' . $this->getOrderId());
        $body = (string)$this->getResponse()->getBody();

        $this->assertStringContainsString('sales_order_view', $body);
        $this->assertStringNotContainsString('id="deleteanyorder"', $body);
    }
}
