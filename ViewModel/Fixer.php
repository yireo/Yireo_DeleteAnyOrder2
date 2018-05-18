<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;
use Yireo\DeleteAnyOrder2\Fixer\Fixer as MainFixer;

class Fixer implements ArgumentInterface
{
    /**
     * @var MainFixer
     */
    private $fixer;

    /**
     * Fixer constructor.
     *
     * @param MainFixer $fixer
     */
    public function __construct(
        MainFixer $fixer
    ) {
        $this->fixer = $fixer;
    }

    /**
     * @return AbstractTable[]
     */
    public function getTables(): array
    {
        return $this->fixer->getTables();
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getOrphansPerTable(AbstractTable $table): int
    {
        return $this->fixer->getOrphansPerTable($table);
    }

    /**
     * @param AbstractTable $table
     *
     * @return int
     */
    public function getTotalsPerTable(AbstractTable $table): int
    {
        return $this->fixer->getTotalsPerTable($table);
    }
}