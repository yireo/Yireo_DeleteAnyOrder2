<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;

/**
 * Class AnalyseDatabase
 *
 * @package Yireo\DeleteAnyOrder2\Command
 */
class AnalyseDatabase extends Command
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * AnalyseDatabase constructor.
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
    protected function configure()
    {
        $this->setName("deleteanyorder:analyse");
        $this->setDescription("Analyse the Magento database for renegade orders.");

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $callback = [$output, 'writeln'];
        $this->fixer->analyse($callback);
    }
}