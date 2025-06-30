<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;
use Tourze\WechatWorkMenuBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    public function test_loader_can_be_instantiated(): void
    {
        // Assert
        $this->assertInstanceOf(AttributeControllerLoader::class, $this->loader);
    }

    public function test_load_method_returns_route_collection(): void
    {
        // Act
        $result = $this->loader->load('dummy_resource');

        // Assert
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function test_autoload_method_returns_route_collection(): void
    {
        // Act
        $result = $this->loader->autoload();

        // Assert
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function test_supports_method_returns_false(): void
    {
        // Act
        $result = $this->loader->supports('any_resource');

        // Assert
        $this->assertFalse($result);
    }

    public function test_supports_method_with_type_returns_false(): void
    {
        // Act
        $result = $this->loader->supports('any_resource', 'any_type');

        // Assert
        $this->assertFalse($result);
    }

    public function test_load_with_null_type(): void
    {
        // Act
        $result = $this->loader->load('dummy_resource', null);

        // Assert
        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }
} 