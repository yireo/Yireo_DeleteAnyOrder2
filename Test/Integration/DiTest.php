<?php
declare(strict_types=1);

namespace Yireo\DeleteAnyOrder2\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;

use PHPUnit\Framework\TestCase;

use Yireo\DeleteAnyOrder2\Fixer\Fixer;

/**
 * Class DiTest
 *
 * @package Yireo\DeleteAnyOrder2\Test\Integration
 */
class DiTest extends TestCase
{
    /**
     * Test whether fetching the fixer through the Object Manager works
     */
    public function testMailerTransportBuilder()
    {
        /** @var Fixer $fixer */
        $fixer = Bootstrap::getObjectManager()->create(Fixer::class);
        $this->assertInstanceOf(Fixer::class, $fixer);
    }
}
