<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Plugin;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class AddButtonToOrderView
{
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * AddButtonToOrderView constructor.
     *
     * @param RequestInterface $request
     * @param UrlInterface $url
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $url
    ){
        $this->request = $request;
        $this->url = $url;
    }

    public function beforeGetItems(ButtonList $subject)
    {
        $orderId = $this->getOrderId();
        if (empty($orderId)) {
            return $orderId;
        }

        $message = __('Are you sure you want to delete this order permanently?');
        $deleteUrl = $this->getDeleteUrl();
        $subject->add(
            'deleteanyorder',
            [
                'label' => __('Delete'),
                'class' => 'edit',
                'onclick' => "confirmSetLocation('{$message}', '{$deleteUrl}')"
            ]
        );

        return;
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        $orderId = $this->getOrderId();
        return $this->url->getUrl('deleteanyorder/order/delete', ['order_id' => $orderId]);
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return (int) $this->request->getParam('order_id');
    }
}