<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\DeleteAnyOrder2\Setup\Patch\Data\GrantSplitAclResources;

class GrantSplitAclResourcesTest extends TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testGrantsNewResourcesToRolesWithIndexResource(): void
    {
        $allowedRoleId = $this->createRole('deleteanyorder_allowed', [
            'Yireo_DeleteAnyOrder2::index' => 'allow',
            'Yireo_DeleteAnyOrder2::delete_order' => 'deny',
        ]);
        $deniedRoleId = $this->createRole('deleteanyorder_denied', [
            'Yireo_DeleteAnyOrder2::index' => 'deny',
        ]);

        Bootstrap::getObjectManager()->create(GrantSplitAclResources::class)->apply();

        $this->assertSame('allow', $this->getPermission($allowedRoleId, 'Yireo_DeleteAnyOrder2::fix'));
        $this->assertSame('allow', $this->getPermission($allowedRoleId, 'Yireo_DeleteAnyOrder2::delete_order'));
        $this->assertSame('', $this->getPermission($deniedRoleId, 'Yireo_DeleteAnyOrder2::fix'));
        $this->assertSame('', $this->getPermission($deniedRoleId, 'Yireo_DeleteAnyOrder2::delete_order'));
    }

    private function createRole(string $roleName, array $rules): int
    {
        $connection = $this->getConnection();
        $connection->insert($this->getTable('authorization_role'), [
            'parent_id' => 0,
            'tree_level' => 1,
            'sort_order' => 0,
            'role_type' => 'G',
            'user_id' => 0,
            'user_type' => '2',
            'role_name' => $roleName,
        ]);
        $roleId = (int)$connection->lastInsertId($this->getTable('authorization_role'));

        foreach ($rules as $resourceId => $permission) {
            $connection->insert($this->getTable('authorization_rule'), [
                'role_id' => $roleId,
                'resource_id' => $resourceId,
                'privileges' => '',
                'permission' => $permission,
            ]);
        }

        return $roleId;
    }

    private function getPermission(int $roleId, string $resourceId): string
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('authorization_rule'), ['permission'])
            ->where('role_id = ?', $roleId)
            ->where('resource_id = ?', $resourceId);

        return (string)$connection->fetchOne($select);
    }

    private function getConnection(): AdapterInterface
    {
        return Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();
    }

    private function getTable(string $tableName): string
    {
        return Bootstrap::getObjectManager()->get(ResourceConnection::class)->getTableName($tableName);
    }
}
