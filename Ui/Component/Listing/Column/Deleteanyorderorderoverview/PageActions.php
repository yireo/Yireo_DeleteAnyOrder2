<?php
namespace Yireo\DeleteAnyOrder2\Ui\Component\Listing\Column\Deleteanyorderorderoverview;

class PageActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = "X";
                if(isset($item["deleteanyorder_order_overview_id"]))
                {
                    $id = $item["deleteanyorder_order_overview_id"];
                }
                $item[$name]["view"] = [
                    "href"=>$this->getContext()->getUrl(
                        "deleteanyorder_order_overview/order/edit",["deleteanyorder_order_overview_id"=>$id]),
                    "label"=>__("Edit")
                ];
            }
        }

        return $dataSource;
    }    
    
}
