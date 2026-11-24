<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Logger;

use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Psr\Log\LoggerInterface;

class AuditLogger
{
    public const ACTOR_CLI = 'cli';

    /**
     * @param LoggerInterface $logger
     * @param AuthSession $authSession
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly AuthSession $authSession,
        private readonly RemoteAddress $remoteAddress
    ) {
    }

    /**
     * @param string $message
     * @param array $context
     * @param string|null $actor
     * @return void
     */
    public function log(string $message, array $context = [], ?string $actor = null): void
    {
        if ($actor === null) {
            $actor = $this->getAdminUsername();
            $context['ip'] = (string)$this->remoteAddress->getRemoteAddress();
        }

        $context['actor'] = $actor;
        $this->logger->info($message, $context);
    }

    /**
     * @return string
     */
    private function getAdminUsername(): string
    {
        $user = $this->authSession->getUser();
        if (!$user) {
            return 'unknown';
        }

        return (string)$user->getUserName();
    }
}
