<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;

/**
 * Class FixDatabase
 *
 * @package Yireo\DeleteAnyOrder2\Command
 */
class FixDatabase extends Command
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * FixDatabase constructor.
     *
     * @param Fixer $fixer
     * @param null $name
     */
    public function __construct(
        Fixer $fixer,
        $name = null
    ) {
        parent::__construct($name);
        $this->fixer = $fixer;
    }

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this->setName("deleteanyorder:fix");
        $this->setDescription("Try to fix the Magento database automatically.");

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $callback = [$output, 'writeln'];
        $this->fixer->fix($callback);

        return Command::SUCCESS;
    }
}
