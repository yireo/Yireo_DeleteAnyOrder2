<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Block\Adminhtml;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class OverviewTest extends AbstractBackendController
{
    /**
     * Setup method
     */
    public function setUp()
    {
        $this->resource = 'Yireo_DeleteAnyOrder2::index';
        $this->uri = 'backend/deleteanyorder/index/index';
        parent::setUp();
    }

    /**
     * Test whether the page contains valid body content
     */
    public function testValidBodyContent()
    {
        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $this->assertContains('Delete Any Order', $body);
    }
}
