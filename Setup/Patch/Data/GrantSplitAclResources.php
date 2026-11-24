<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Setup\Patch\Data;

use Magento\Framework\Acl\Data\CacheInterface as AclDataCache;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class GrantSplitAclResources implements DataPatchInterface
{
    private const PARENT_RESOURCE = 'Yireo_DeleteAnyOrder2::index';

    private const NEW_RESOURCES = [
        'Yireo_DeleteAnyOrder2::fix',
        'Yireo_DeleteAnyOrder2::delete_order',
    ];

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AclDataCache $aclDataCache
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly AclDataCache $aclDataCache
    ) {
    }

    /**
     * @return $this
     */
    public function apply(): self
    {
        $connection = $this->moduleDataSetup->getConnection();
        $ruleTable = $this->moduleDataSetup->getTable('authorization_rule');
        if (!$connection->isTableExists($ruleTable)) {
            return $this;
        }

        $roleIds = $connection->fetchCol(
            $connection->select()
                ->from($ruleTable, ['role_id'])
                ->where('resource_id = ?', self::PARENT_RESOURCE)
                ->where('permission = ?', 'allow')
        );

        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            foreach (self::NEW_RESOURCES as $resourceId) {
                $this->grant($roleId, $resourceId);
            }
        }

        $this->aclDataCache->clean();

        return $this;
    }

    /**
     * @param int $roleId
     * @param string $resourceId
     * @return void
     */
    private function grant(int $roleId, string $resourceId): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $ruleTable = $this->moduleDataSetup->getTable('authorization_rule');
        $where = ['role_id = ?' => $roleId, 'resource_id = ?' => $resourceId];

        $existingRuleIds = $connection->fetchCol(
            $connection->select()
                ->from($ruleTable, ['rule_id'])
                ->where('role_id = ?', $roleId)
                ->where('resource_id = ?', $resourceId)
        );

        if (!empty($existingRuleIds)) {
            $connection->update($ruleTable, ['permission' => 'allow'], $where);
            return;
        }

        $connection->insert($ruleTable, [
            'role_id' => $roleId,
            'resource_id' => $resourceId,
            'privileges' => '',
            'permission' => 'allow',
        ]);
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
