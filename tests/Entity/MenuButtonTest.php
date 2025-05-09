<?php

namespace WechatWorkMenuBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Entity\Corp;
use WechatWorkMenuBundle\Entity\MenuButton;
use WechatWorkMenuBundle\Enum\MenuButtonType;

class MenuButtonTest extends TestCase
{
    private MenuButton $menuButton;
    private Corp $corp;
    private Agent $agent;
    
    protected function setUp(): void
    {
        $this->menuButton = new MenuButton();
        
        // 模拟Corp对象
        $this->corp = $this->createMock(Corp::class);
        
        // 模拟Agent对象
        $this->agent = $this->createMock(Agent::class);
    }
    
    public function testConstructor_initializesCollections(): void
    {
        // 测试构造函数是否正确初始化了children集合
        $this->assertInstanceOf(ArrayCollection::class, $this->menuButton->getChildren());
        $this->assertTrue($this->menuButton->getChildren()->isEmpty());
    }
    
    public function testBasicProperties_gettersAndSetters(): void
    {
        // 测试名称字段
        $name = 'Test Button';
        $this->menuButton->setName($name);
        $this->assertSame($name, $this->menuButton->getName());
        
        // 测试类型字段
        $type = MenuButtonType::Click;
        $this->menuButton->setType($type);
        $this->assertSame($type, $this->menuButton->getType());
        
        // 测试点击键字段
        $clickKey = 'key_123';
        $this->menuButton->setClickKey($clickKey);
        $this->assertSame($clickKey, $this->menuButton->getClickKey());
        
        // 测试URL字段
        $viewUrl = 'https://example.com';
        $this->menuButton->setViewUrl($viewUrl);
        $this->assertSame($viewUrl, $this->menuButton->getViewUrl());
        
        // 测试小程序AppID
        $miniProgramAppId = 'wx123456';
        $this->menuButton->setMiniProgramAppId($miniProgramAppId);
        $this->assertSame($miniProgramAppId, $this->menuButton->getMiniProgramAppId());
        
        // 测试小程序路径
        $miniProgramPath = 'pages/index/index';
        $this->menuButton->setMiniProgramPath($miniProgramPath);
        $this->assertSame($miniProgramPath, $this->menuButton->getMiniProgramPath());
        
        // 测试排序号
        $sortNumber = 100;
        $this->menuButton->setSortNumber($sortNumber);
        $this->assertSame($sortNumber, $this->menuButton->getSortNumber());
        
        // 测试有效状态
        $valid = true;
        $this->menuButton->setValid($valid);
        $this->assertSame($valid, $this->menuButton->isValid());
        
        // 测试公司关联
        $this->menuButton->setCorp($this->corp);
        $this->assertSame($this->corp, $this->menuButton->getCorp());
        
        // 测试应用关联
        $this->menuButton->setAgent($this->agent);
        $this->assertSame($this->agent, $this->menuButton->getAgent());
    }
    
    public function testRelationship_parentChild(): void
    {
        // 创建父按钮和子按钮
        $parentButton = new MenuButton();
        $parentButton->setName('Parent Button');
        
        $childButton1 = new MenuButton();
        $childButton1->setName('Child Button 1');
        
        $childButton2 = new MenuButton();
        $childButton2->setName('Child Button 2');
        
        // 测试设置父按钮
        $childButton1->setParent($parentButton);
        $this->assertSame($parentButton, $childButton1->getParent());
        
        // 测试添加子按钮
        $parentButton->addChild($childButton1);
        $parentButton->addChild($childButton2);
        
        // 验证子按钮集合
        $this->assertCount(2, $parentButton->getChildren());
        $this->assertTrue($parentButton->getChildren()->contains($childButton1));
        $this->assertTrue($parentButton->getChildren()->contains($childButton2));
        
        // 测试移除子按钮
        $parentButton->removeChild($childButton1);
        $this->assertCount(1, $parentButton->getChildren());
        $this->assertFalse($parentButton->getChildren()->contains($childButton1));
        $this->assertTrue($parentButton->getChildren()->contains($childButton2));
        
        // 测试重复添加相同子按钮
        $parentButton->addChild($childButton2);
        $this->assertCount(1, $parentButton->getChildren());
    }
    
    public function testMetadataFields_trackingFunctionality(): void
    {
        // 测试createTime字段
        $createTime = new \DateTime();
        $this->menuButton->setCreateTime($createTime);
        $this->assertSame($createTime, $this->menuButton->getCreateTime());
        
        // 测试updateTime字段
        $updateTime = new \DateTime();
        $this->menuButton->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->menuButton->getUpdateTime());
        
        // 测试createdBy字段
        $createdBy = 'admin';
        $this->menuButton->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $this->menuButton->getCreatedBy());
        
        // 测试updatedBy字段
        $updatedBy = 'manager';
        $this->menuButton->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $this->menuButton->getUpdatedBy());
    }
    
    public function testApiArray_containsCorrectData(): void
    {
        // 准备测试数据
        $this->menuButton->setName('API Button');
        $this->menuButton->setType(MenuButtonType::Click);
        $this->menuButton->setClickKey('api_key');
        $this->menuButton->setSortNumber(50);
        $this->menuButton->setValid(true);
        
        // 获取API数组
        $apiArray = $this->menuButton->retrieveApiArray();
        
        // 验证API数组包含预期的数据
        $this->assertIsArray($apiArray);
        $this->assertArrayHasKey('name', $apiArray);
        $this->assertArrayHasKey('type', $apiArray);
        $this->assertArrayHasKey('key', $apiArray);
        
        $this->assertEquals('API Button', $apiArray['name']);
        $this->assertEquals('click', $apiArray['type']);
        $this->assertEquals('api_key', $apiArray['key']);
    }
    
    public function testEdgeCases_nullValues(): void
    {
        // 测试可空字段的空值处理
        $this->menuButton->setClickKey(null);
        $this->assertNull($this->menuButton->getClickKey());
        
        $this->menuButton->setViewUrl(null);
        $this->assertNull($this->menuButton->getViewUrl());
        
        $this->menuButton->setType(null);
        $this->assertNull($this->menuButton->getType());
        
        $this->menuButton->setMiniProgramAppId(null);
        $this->assertNull($this->menuButton->getMiniProgramAppId());
        
        $this->menuButton->setMiniProgramPath(null);
        $this->assertNull($this->menuButton->getMiniProgramPath());
        
        $this->menuButton->setParent(null);
        $this->assertNull($this->menuButton->getParent());
    }
    
    public function testEdgeCases_longValues(): void
    {
        // 测试长文本处理
        $longName = str_repeat('a', 120);
        $this->menuButton->setName($longName);
        $this->assertSame($longName, $this->menuButton->getName());
        
        $longUrl = 'https://example.com/' . str_repeat('path/', 50);
        $this->menuButton->setViewUrl($longUrl);
        $this->assertSame($longUrl, $this->menuButton->getViewUrl());
    }
} 