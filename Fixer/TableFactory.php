<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Fixer;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class TableFactory
 *
 * @package Yireo\DeleteAnyOrder2\Fixer
 */
class TableFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * TableFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $tableClass
     *
     * @return AbstractTable
     */
    public function create(string $tableClass): AbstractTable
    {
        $table = $this->objectManager->get($tableClass);
        return $table;
    }
}
