<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;
use Tourze\WechatWorkMenuBundle\DependencyInjection\WechatWorkMenuExtension;

/**
 * @internal
 */
#[CoversClass(WechatWorkMenuExtension::class)]
final class WechatWorkMenuExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatWorkMenuExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new WechatWorkMenuExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testExtensionInheritsFromAutoExtension(): void
    {
        // Assert - 验证继承关系
        $this->assertInstanceOf(AutoExtension::class, $this->extension);
    }
}
