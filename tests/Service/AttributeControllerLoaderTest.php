<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;
use Tourze\WechatWorkMenuBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
    }

    public function testLoaderImplementsCorrectInterfaces(): void
    {
        // Assert - 验证实现了必要的接口
        $this->assertInstanceOf(RoutingAutoLoaderInterface::class, $this->loader);
        $this->assertInstanceOf(Loader::class, $this->loader);
    }

    public function testLoadMethodDelegatesToAutoload(): void
    {
        // Act
        $loadResult = $this->loader->load('dummy_resource');
        $autoloadResult = $this->loader->autoload();

        // Assert - 验证 load 方法实际上调用了 autoload
        $this->assertInstanceOf(RouteCollection::class, $loadResult);
        $this->assertEquals($autoloadResult->all(), $loadResult->all());
    }

    public function testAutoloadMethodLoadsControllerRoutes(): void
    {
        // Act
        $result = $this->loader->autoload();

        // Assert - 验证返回有效的路由集合
        $this->assertInstanceOf(RouteCollection::class, $result);
        // 验证路由集合不为空（如果控制器有路由的话）
        $routes = $result->all();
        $this->assertIsArray($routes);
    }

    public function testSupportsMethodReturnsFalse(): void
    {
        // Act
        $result = $this->loader->supports('any_resource');

        // Assert
        $this->assertFalse($result);
    }

    public function testSupportsMethodWithTypeReturnsFalse(): void
    {
        // Act
        $result = $this->loader->supports('any_resource', 'any_type');

        // Assert
        $this->assertFalse($result);
    }

    public function testLoadWithNullTypeWorksIdentically(): void
    {
        // Act
        $resultWithNull = $this->loader->load('dummy_resource', null);
        $resultWithoutType = $this->loader->load('dummy_resource');

        // Assert - 验证 null 类型参数不影响结果
        $this->assertEquals($resultWithNull->all(), $resultWithoutType->all());
    }
}
