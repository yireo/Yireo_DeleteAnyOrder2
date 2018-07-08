<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Unit\ViewModel;

use Mockery;
use Yireo\DeleteAnyOrder2\ViewModel\Fixer as TestTarget;
use Yireo\DeleteAnyOrder2\Fixer\Fixer as MainFixer;
use Yireo\DeleteAnyOrder2\Fixer\AbstractTable;

/**
 * Class FixerTest
 * @package Yireo\DeleteAnyOrder2\Test\Unit\ViewModel
 */
class FixerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the behaviour of getTables()
     */
    public function testGetTables()
    {
        $target = $this->getTestTarget();
        $this->assertNotEquals($target->getTables(), ['something']);
        $this->assertEquals($target->getTables(), $this->getMainFixerTableStub());
    }

    /**
     * Test the behaviour of getOrphansCountPerTable()
     */
    public function testGetOrphansCountPerTable()
    {
        $target = $this->getTestTarget();
        $table = $this->getMainFixerTable();

        $this->assertNotEmpty($table);
        $this->assertNotEquals($target->getOrphansCountPerTable($table), 0);
        $this->assertEquals($target->getOrphansCountPerTable($table), $this->getMainFixerOrphansCountStub());
    }

    /**
     * Test the behaviour of getTotalsPerTable()
     */
    public function testGetTotalsPerTable()
    {
        $target = $this->getTestTarget();
        $table = $this->getMainFixerTable();

        $this->assertNotEmpty($table);
        $this->assertNotEquals($target->getTotalsPerTable($table), 0);
        $this->assertEquals($target->getTotalsPerTable($table), $this->getMainFixerTotalsStub());
    }

    /**
     * @return TestTarget
     */
    private function getTestTarget(): TestTarget
    {
        $mainFixer = $this->getMainFixer();
        return new TestTarget($mainFixer);
    }

    /**
     * @return MainFixer
     */
    private function getMainFixer(): MainFixer
    {
        $mainFixer = Mockery::mock(MainFixer::class);
        $mainFixer->allows()->getTables()->andReturns($this->getMainFixerTableStub());

        $table = $this->getMainFixerTable();
        $mainFixer->allows()->getOrphansCountPerTable($table)->andReturns($this->getMainFixerOrphansCountStub());

        $table = $this->getMainFixerTable();
        $mainFixer->allows()->getTotalsPerTable($table)->andReturns($this->getMainFixerTotalsStub());

        return $mainFixer;
    }

    /**
     * @return array
     */
    private function getMainFixerTableStub(): array
    {
        static $tables = null;

        if (empty($tables)) {
            $tables = [
                Mockery::mock(AbstractTable::class)
            ];
        }

        return $tables;
    }

    /**
     * @return AbstractTable
     */
    private function getMainFixerTable(): AbstractTable
    {
        $tables = $this->getMainFixerTableStub();
        return array_shift($tables);
    }

    /**
     * @return int
     */
    private function getMainFixerOrphansCountStub(): int
    {
        return 42;
    }

    /**
     * @return int
     */
    private function getMainFixerTotalsStub(): int
    {
        return 100;
    }
}
