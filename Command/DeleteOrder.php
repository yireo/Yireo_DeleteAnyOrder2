<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Command;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Sales\Api\OrderRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteOrder
 *
 * @package Yireo\DeleteAnyOrder2\Command
 */
class DeleteOrder extends Command
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * DeleteOrder constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param State $state
     * @param Registry $registry
     * @param null $name
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        State $state,
        Registry $registry,
        $name = null
    ) {
        parent::__construct($name);
        $this->orderRepository = $orderRepository;
        $this->state = $state;
        $this->registry = $registry;
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName("deleteanyorder:delete");
        $this->setDescription("Delete an order from the Magento database.");
        $this->addArgument(
            'order_id',
            InputArgument::REQUIRED,
            'Order ID of the order you want to delete'
        );

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orderId = $input->getArgument('order_id');

        if (empty($orderId)) {
            $output->writeln('Invalid order ID');
            return;
        }

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $this->registry->register('isSecureArea', true);

        $order = $this->orderRepository->get($orderId);
        $this->orderRepository->delete($order);

        $output->writeln("Removed order succesfully");
    }
}
