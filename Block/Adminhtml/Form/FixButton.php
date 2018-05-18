<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Block\Adminhtml\Form;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class FixButton
 */
class FixButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Fix Database'),
            'class' => 'save primary',
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'button' => [
                        'event' => 'save'
                    ]
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
