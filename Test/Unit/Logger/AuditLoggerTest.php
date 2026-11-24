<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Unit\Logger;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yireo\DeleteAnyOrder2\Logger\AuditLogger;

class AuditLoggerTest extends TestCase
{
    public function testLogsCliActorWithoutIp(): void
    {
        $remoteAddress = $this->createMock(RemoteAddress::class);
        $remoteAddress->expects($this->never())->method('getRemoteAddress');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')
            ->with('Deleted order', ['order_id' => 42, 'actor' => 'cli']);

        $auditLogger = new AuditLogger($logger, $this->createMock(AuthSession::class), $remoteAddress);
        $auditLogger->log('Deleted order', ['order_id' => 42], AuditLogger::ACTOR_CLI);
    }

    public function testLogsUnknownAdminWithIp(): void
    {
        $remoteAddress = $this->createMock(RemoteAddress::class);
        $remoteAddress->method('getRemoteAddress')->willReturn('127.0.0.1');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info')
            ->with('Deleted order', ['order_id' => 42, 'ip' => '127.0.0.1', 'actor' => 'unknown']);

        $auditLogger = new AuditLogger($logger, $this->createMock(AuthSession::class), $remoteAddress);
        $auditLogger->log('Deleted order', ['order_id' => 42]);
    }
}
