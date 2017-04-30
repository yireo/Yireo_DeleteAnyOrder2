<?php
namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::delete';  
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/index/index');
        return $resultRedirect;
    }     
}
