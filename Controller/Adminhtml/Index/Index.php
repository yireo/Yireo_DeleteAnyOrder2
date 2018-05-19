<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index
 */
class Index extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::index';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * MassReindex constructor.
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Yireo DeleteAnyOrder'));

        return $resultPage;
    }
}
