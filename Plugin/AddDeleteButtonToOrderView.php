<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Plugin;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View as OrderView;
use Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order\Delete;

class AddDeleteButtonToOrderView
{
    /**
     * @param AuthorizationInterface $authorization
     * @param Escaper $escaper
     */
    public function __construct(
        private readonly AuthorizationInterface $authorization,
        private readonly Escaper $escaper
    ) {
    }

    /**
     * @param OrderView $subject
     * @param LayoutInterface $layout
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSetLayout(OrderView $subject, LayoutInterface $layout): void
    {
        $orderId = (int)$subject->getOrderId();
        if ($orderId < 1 || !$this->authorization->isAllowed(Delete::ADMIN_RESOURCE)) {
            return;
        }

        $message = (string)__('Are you sure you want to delete this order permanently?');
        $deleteUrl = $subject->getUrl('deleteanyorder/order/delete', ['order_id' => $orderId]);

        $subject->addButton(
            'deleteanyorder',
            [
                'label' => __('Delete'),
                'class' => 'delete',
                'onclick' => sprintf(
                    "deleteConfirm('%s', '%s', {data: {}})",
                    $this->escaper->escapeJs($message),
                    $this->escaper->escapeJs($deleteUrl)
                ),
            ]
        );
    }
}
