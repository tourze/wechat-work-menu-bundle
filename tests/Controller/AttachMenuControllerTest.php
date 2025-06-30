<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;

class AttachMenuControllerTest extends TestCase
{
    public function testCrm1_methodExists(): void
    {
        // 验证方法存在
        $this->assertFileExists(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        $controllerFile = file_get_contents(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        $this->assertStringContainsString('public function crm1()', $controllerFile);
    }
    
    public function testCrm1_methodContentAndStructure(): void
    {
        $controllerFile = file_get_contents(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        
        // 验证方法内容包含预期结构
        $this->assertStringContainsString('public function crm1()', $controllerFile);
        $this->assertStringContainsString('return $this->json(', $controllerFile);
        $this->assertStringContainsString('time', $controllerFile);
        $this->assertStringContainsString('method', $controllerFile);
    }
    
    public function testCrm2_methodExists(): void
    {
        // 验证方法存在
        $this->assertFileExists(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        $controllerFile = file_get_contents(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        $this->assertStringContainsString('public function crm2()', $controllerFile);
    }
    
    public function testCrm2_methodContentAndStructure(): void
    {
        $controllerFile = file_get_contents(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        
        // 验证方法内容包含预期结构
        $this->assertStringContainsString('public function crm2()', $controllerFile);
        $this->assertStringContainsString('return $this->json(', $controllerFile);
        $this->assertStringContainsString('time', $controllerFile);
        $this->assertStringContainsString('method', $controllerFile);
    }
    
    public function testBothMethods_followSamePattern(): void
    {
        $controllerFile = file_get_contents(__DIR__ . '/../../src/Controller/AttachMenuController.php');
        
        // 解析方法体
        preg_match('/public function crm1\(\)[^{]*{(.*?)}/s', $controllerFile, $crm1Body);
        preg_match('/public function crm2\(\)[^{]*{(.*?)}/s', $controllerFile, $crm2Body);
        
        // 移除方法名称的差异，比较主体结构
        $crm1Pattern = str_replace('crm1', 'METHOD', $crm1Body[1] ?? '');
        $crm2Pattern = str_replace('crm2', 'METHOD', $crm2Body[1] ?? '');
        
        // 检查两个方法是否遵循相同的模式
        $this->assertEquals($crm1Pattern, $crm2Pattern);
    }
} 