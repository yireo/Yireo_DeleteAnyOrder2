<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Yireo\DeleteAnyOrder2\Fixer\Fixer;
use Yireo\DeleteAnyOrder2\Logger\AuditLogger;

class FixDatabase extends Command
{
    /**
     * @param Fixer $fixer
     * @param AuditLogger $auditLogger
     * @param string|null $name
     */
    public function __construct(
        private readonly Fixer $fixer,
        private readonly AuditLogger $auditLogger,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this->setName("deleteanyorder:fix");
        $this->setDescription("Try to fix the Magento database automatically.");
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Only show the orphans that would be removed');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Fix without asking for confirmation');

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

        if ($input->getOption('dry-run')) {
            $this->fixer->analyse($callback);
            return Command::SUCCESS;
        }

        if (!$this->isConfirmed($input, $output)) {
            $output->writeln('Aborted');
            return Command::FAILURE;
        }

        $deletedRows = $this->fixer->fix($callback);
        $this->auditLogger->log('Fixed the database', ['deleted_rows' => $deletedRows], AuditLogger::ACTOR_CLI);

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function isConfirmed(InputInterface $input, OutputInterface $output): bool
    {
        if ($input->getOption('force') || !$input->isInteractive()) {
            return true;
        }

        $question = new ConfirmationQuestion(
            'This permanently deletes all order orphans from the database. Continue? [y/N] ',
            false
        );

        return (bool)(new QuestionHelper())->ask($input, $output, $question);
    }
}
