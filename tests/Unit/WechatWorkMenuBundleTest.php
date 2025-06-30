<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\WechatWorkMenuBundle\WechatWorkMenuBundle;

class WechatWorkMenuBundleTest extends TestCase
{
    private WechatWorkMenuBundle $bundle;
    
    protected function setUp(): void
    {
        $this->bundle = new WechatWorkMenuBundle();
    }
    
    public function testBundleInheritance(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }
    
    public function testBundleName(): void
    {
        $this->assertSame('WechatWorkMenuBundle', $this->bundle->getName());
    }
    
    public function testBundleNamespace(): void
    {
        $this->assertSame('Tourze\\WechatWorkMenuBundle', $this->bundle->getNamespace());
    }
    
    public function testBundlePath(): void
    {
        $bundlePath = $this->bundle->getPath();
        $this->assertStringContainsString('wechat-work-menu-bundle', $bundlePath);
        $this->assertStringContainsString('src', $bundlePath);
    }
}