<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Block\Adminhtml;

use Magento\Framework\Exception\AuthenticationException;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Magento\TestFramework\Request;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page\Interceptor as ResultPage;
use Magento\Backend\App\Action\Context as ActionContext;
use Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index\Index;

/**
 *
 */
class OverviewTest extends AbstractBackendController
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_DeleteAnyOrder2::index';
        $this->uri = 'backend/deleteanyorder/index/index';
    }

    /**
     *
     */
    public function testReturnsResultInstance()
    {
        $context = Bootstrap::getObjectManager()->create(ActionContext::class);
        $resultPageFactory = new PageFactory(Bootstrap::getObjectManager());
        $controller = new Index($context, $resultPageFactory);
        $result = $controller->execute();
        $this->assertInstanceOf(ResultPage::class, $result);
    }

    /**
     *
     */
    public function testCanHandleGetRequests()
    {
        $this->getRequest()->setMethod(Request::METHOD_GET);
        $this->dispatch($this->uri);
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * Test whether the page contains valid body content
     */
    public function testValidBodyContent()
    {
        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $this->assertTrue((bool)strpos($body, 'Delete Any Order'));
    }
}
