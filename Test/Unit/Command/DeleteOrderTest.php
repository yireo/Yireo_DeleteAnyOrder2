<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Unit\Command;

use Magento\Framework\App\State;
use Magento\Framework\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Yireo\DeleteAnyOrder2\Command\DeleteOrder;
use Yireo\DeleteAnyOrder2\Logger\AuditLogger;
use Yireo\DeleteAnyOrder2\Order\OrderDeleter;

class DeleteOrderTest extends TestCase
{
    public static function getInvalidOrderIds(): array
    {
        return [
            ['0'],
            ['-1'],
            ['1e3'],
            ['12abc'],
            [' 12'],
            ['1.5'],
        ];
    }

    #[DataProvider('getInvalidOrderIds')]
    public function testRejectsInvalidOrderIds(string $orderId): void
    {
        $orderDeleter = $this->createMock(OrderDeleter::class);
        $orderDeleter->expects($this->never())->method('delete');

        $commandTester = new CommandTester($this->createCommand($orderDeleter));
        $result = $commandTester->execute(['order_id' => $orderId], ['interactive' => false]);

        $this->assertSame(Command::FAILURE, $result);
        $this->assertStringContainsString('Invalid order ID', $commandTester->getDisplay());
    }

    public function testDoesNotDeleteWithoutConfirmation(): void
    {
        $orderDeleter = $this->createMock(OrderDeleter::class);
        $orderDeleter->expects($this->never())->method('delete');

        $commandTester = new CommandTester($this->createCommand($orderDeleter));
        $commandTester->setInputs(['no']);
        $result = $commandTester->execute(['order_id' => '42'], ['interactive' => true]);

        $this->assertSame(Command::FAILURE, $result);
    }

    public function testDeletesOrderWithForce(): void
    {
        $orderDeleter = $this->createMock(OrderDeleter::class);
        $orderDeleter->expects($this->once())->method('delete')->with(42, AuditLogger::ACTOR_CLI);

        $commandTester = new CommandTester($this->createCommand($orderDeleter));
        $result = $commandTester->execute(['order_id' => '42', '--force' => true], ['interactive' => true]);

        $this->assertSame(Command::SUCCESS, $result);
    }

    private function createCommand(OrderDeleter $orderDeleter): DeleteOrder
    {
        return new DeleteOrder(
            $orderDeleter,
            $this->createMock(State::class),
            $this->createMock(Registry::class)
        );
    }
}
