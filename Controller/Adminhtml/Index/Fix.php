<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;

/**
 * Class Fix
 *
 * @package Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index
 */
class Fix extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::index';

    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * MassReindex constructor.
     *
     * @param Context $context
     * @param Fixer $fixer
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        Fixer $fixer,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->fixer = $fixer;
        $this->redirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $callback = [$this->messageManager, 'addNoticeMessage'];
        $this->fixer->fix($callback);

        $this->messageManager->addNoticeMessage('Fixed the database');

        $redirect = $this->redirectFactory->create();
        $redirect->setPath('deleteanyorder/index/index');
        return $redirect;
    }
}
