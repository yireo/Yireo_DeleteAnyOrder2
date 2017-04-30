<?php
namespace Yireo\DeleteAnyOrder2\Ui\Component\Listing\DataProviders\Deleteanyorder\Order;

class Overview extends \Magento\Ui\DataProvider\AbstractDataProvider
{    
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
