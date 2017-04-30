<?php
namespace Yireo\DeleteAnyOrder2\Controller\Adminhtml\Index;
class Index extends \Magento\Backend\App\Action
{
    
    const ADMIN_RESOURCE = 'ACL RULE HERE';       
        
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
