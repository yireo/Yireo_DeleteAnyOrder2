<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Observer\Order\Delete;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Cleanup
 *
 * @package Yireo\DeleteAnyOrder2\Observer\Deleteanyorder2\Order\Delete
 */
class Cleanup implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @return ObserverInterface
     */
    public function execute(Observer $observer)
    {
        return $this;
    }
}
