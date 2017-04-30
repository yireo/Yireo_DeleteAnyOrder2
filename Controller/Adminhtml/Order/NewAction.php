<?php
namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Order;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Yireo_DeleteAnyOrder2::delete';       
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;        
        return parent::__construct($context);
    }
    
    public function execute()
    {
        return $this->resultPageFactory->create();  
    }    
}
