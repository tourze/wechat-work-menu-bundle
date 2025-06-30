<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\WechatWorkMenuBundle\DependencyInjection\WechatWorkMenuExtension;

class WechatWorkMenuExtensionTest extends TestCase
{
    private WechatWorkMenuExtension $extension;
    private ContainerBuilder $container;

    public function test_load_can_handle_empty_configs(): void
    {
        // Act & Assert
        $this->extension->load([], $this->container);
        $this->assertTrue(true); // 如果没有抛出异常，测试通过
    }

    public function test_extension_can_be_instantiated(): void
    {
        // Assert
        $this->assertInstanceOf(WechatWorkMenuExtension::class, $this->extension);
    }

    public function test_load_does_not_throw_exception(): void
    {
        // Act & Assert
        $this->extension->load([], $this->container);
        $this->assertTrue(true); // 如果没有抛出异常，测试通过
    }

    protected function setUp(): void
    {
        $this->extension = new WechatWorkMenuExtension();
        $this->container = new ContainerBuilder();
    }
} 