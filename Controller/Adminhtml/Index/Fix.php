<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;
use Yireo\DeleteAnyOrder2\Logger\AuditLogger;

class Fix extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::fix';

    /**
     * @param Context $context
     * @param Fixer $fixer
     * @param AuditLogger $auditLogger
     */
    public function __construct(
        Context $context,
        private readonly Fixer $fixer,
        private readonly AuditLogger $auditLogger
    ) {
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $callback = function (string $message): void {
            $this->messageManager->addNoticeMessage($message);
        };

        $deletedRows = $this->fixer->fix($callback);
        $this->auditLogger->log('Fixed the database', ['deleted_rows' => $deletedRows]);
        $this->messageManager->addNoticeMessage(__('Fixed the database'));

        return $this->resultRedirectFactory->create()->setPath('deleteanyorder/index/index');
    }
}
