<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkMenuBundle\WechatWorkMenuBundle;

class BundleIntegrationTest extends TestCase
{
    public function testBundleInstance(): void
    {
        // 创建Bundle实例
        $bundle = new WechatWorkMenuBundle();
        
        // 验证实例类型
        $this->assertInstanceOf(WechatWorkMenuBundle::class, $bundle);
    }
} 