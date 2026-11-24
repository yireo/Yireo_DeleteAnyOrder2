<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Yireo\DeleteAnyOrder2\Logger\AuditLogger;
use Yireo\DeleteAnyOrder2\Order\OrderDeleter;

class DeleteOrder extends Command
{
    /**
     * @param OrderDeleter $orderDeleter
     * @param State $state
     * @param Registry $registry
     * @param string|null $name
     */
    public function __construct(
        private readonly OrderDeleter $orderDeleter,
        private readonly State $state,
        private readonly Registry $registry,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Configure the command
     */
    protected function configure(): void
    {
        $this->setName("deleteanyorder:delete");
        $this->setDescription("Delete an order from the Magento database.");
        $this->addArgument(
            'order_id',
            InputArgument::REQUIRED,
            'Order ID (entity ID) of the order you want to delete'
        );
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Delete without asking for confirmation');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orderId = (string)$input->getArgument('order_id');
        if (!ctype_digit($orderId) || (int)$orderId < 1) {
            $output->writeln('<error>Invalid order ID</error>');
            return Command::FAILURE;
        }

        if (!$this->isConfirmed($input, $output, (int)$orderId)) {
            $output->writeln('Aborted');
            return Command::FAILURE;
        }

        $this->initAreaCode();
        $this->registry->register('isSecureArea', true, true);

        try {
            $this->orderDeleter->delete((int)$orderId, AuditLogger::ACTOR_CLI);
        } catch (Exception $exception) {
            $message = OutputFormatter::escape($exception->getMessage());
            $output->writeln('<error>Unable to delete order: ' . $message . '</error>');
            return Command::FAILURE;
        }

        $output->writeln("Removed order successfully");

        return Command::SUCCESS;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    private function initAreaCode(): void
    {
        try {
            $this->state->getAreaCode();
        } catch (LocalizedException $exception) {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param int $orderId
     * @return bool
     */
    private function isConfirmed(InputInterface $input, OutputInterface $output, int $orderId): bool
    {
        if ($input->getOption('force') || !$input->isInteractive()) {
            return true;
        }

        $question = new ConfirmationQuestion(
            sprintf('Are you sure you want to delete order %d permanently? [y/N] ', $orderId),
            false
        );

        return (bool)(new QuestionHelper())->ask($input, $output, $question);
    }
}
