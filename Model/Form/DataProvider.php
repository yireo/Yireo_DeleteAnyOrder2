<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Model\Form;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->loadedData = [];

        return $this->loadedData;
    }

    /**
     * Dummy method to satisfy the UiComponent mechanism
     *
     * @param Filter $filter
     *
     * @return mixed|null|void
     */
    public function addFilter(Filter $filter)
    {
        return null;
    }
}
