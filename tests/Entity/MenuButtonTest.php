<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;
use Tourze\WechatWorkMenuBundle\Exception\MenuButtonException;

/**
 * @internal
 */
#[CoversClass(MenuButton::class)]
final class MenuButtonTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new MenuButton();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name' => ['name', 'test_value'],
        ];
    }

    public function testConstructor(): void
    {
        $menuButton = new MenuButton();

        $this->assertNull($menuButton->getId());
        $this->assertNull($menuButton->getAgent());
        $this->assertNull($menuButton->getCorp());
        $this->assertCount(0, $menuButton->getChildren());
        $this->assertSame(0, $menuButton->getSortNumber());
        $this->assertFalse($menuButton->isValid());
    }

    public function testSetName(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setName('测试菜单');

        $this->assertSame('测试菜单', $menuButton->getName());
    }

    public function testSetAgent(): void
    {
        $menuButton = new MenuButton();
        $agent = $this->createMock(AgentInterface::class);
        $menuButton->setAgent($agent);

        $this->assertSame($agent, $menuButton->getAgent());
    }

    public function testSetCorp(): void
    {
        $menuButton = new MenuButton();
        $corp = $this->createMock(CorpInterface::class);
        $menuButton->setCorp($corp);

        $this->assertSame($corp, $menuButton->getCorp());
    }

    public function testSetType(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setType(MenuButtonType::Click);

        $this->assertSame(MenuButtonType::Click, $menuButton->getType());
    }

    public function testSetClickKey(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setClickKey('test_key');

        $this->assertSame('test_key', $menuButton->getClickKey());
    }

    public function testSetViewUrl(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setViewUrl('https://example.com');

        $this->assertSame('https://example.com', $menuButton->getViewUrl());
    }

    public function testSetMiniProgramAppId(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setMiniProgramAppId('wx123456');

        $this->assertSame('wx123456', $menuButton->getMiniProgramAppId());
    }

    public function testSetMiniProgramPath(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setMiniProgramPath('/pages/index/index');

        $this->assertSame('/pages/index/index', $menuButton->getMiniProgramPath());
    }

    public function testParentChildRelationship(): void
    {
        $parent = new MenuButton();
        $parent->setName('父菜单');

        $child = new MenuButton();
        $child->setName('子菜单');

        // 测试添加子菜单
        $parent->addChild($child);
        $this->assertSame($parent, $child->getParent());
        $this->assertTrue($parent->getChildren()->contains($child));

        // 测试移除子菜单
        $parent->removeChild($child);
        $this->assertNull($child->getParent());
        $this->assertFalse($parent->getChildren()->contains($child));
    }

    public function testSetSortNumber(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setSortNumber(10);

        $this->assertSame(10, $menuButton->getSortNumber());
    }

    public function testSetValid(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setValid(true);

        $this->assertTrue($menuButton->isValid());
    }

    public function testRetrieveApiArrayWithSubMenu(): void
    {
        $parent = new MenuButton();
        $parent->setName('父菜单');

        $child = new MenuButton();
        $child->setName('子菜单');
        $child->setType(MenuButtonType::Click);
        $child->setClickKey('child_key');

        $parent->addChild($child);

        $result = $parent->retrieveApiArray();

        $this->assertSame('父菜单', $result['name']);
        $this->assertArrayHasKey('sub_button', $result);
        $this->assertIsArray($result['sub_button']);
        $subButtons = $result['sub_button'];
        $this->assertCount(1, $subButtons);
        $this->assertArrayHasKey(0, $subButtons);
        $this->assertIsArray($subButtons[0]);
        $firstChild = $subButtons[0];
        $this->assertSame('子菜单', $firstChild['name']);
        $this->assertSame('click', $firstChild['type']);
        $this->assertSame('child_key', $firstChild['key']);
    }

    public function testRetrieveApiArrayWithClickType(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setName('点击菜单');
        $menuButton->setType(MenuButtonType::Click);
        $menuButton->setClickKey('test_key');

        $result = $menuButton->retrieveApiArray();

        $this->assertSame('click', $result['type']);
        $this->assertSame('点击菜单', $result['name']);
        $this->assertSame('test_key', $result['key']);
    }

    public function testRetrieveApiArrayWithViewType(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setName('链接菜单');
        $menuButton->setType(MenuButtonType::View);
        $menuButton->setViewUrl('https://example.com');

        $result = $menuButton->retrieveApiArray();

        $this->assertSame('view', $result['type']);
        $this->assertSame('链接菜单', $result['name']);
        $this->assertSame('https://example.com', $result['url']);
    }

    public function testRetrieveApiArrayWithMiniProgramType(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setName('小程序菜单');
        $menuButton->setType(MenuButtonType::ViewMiniProgram);
        $menuButton->setMiniProgramAppId('wx123456');
        $menuButton->setMiniProgramPath('/pages/index/index');

        $result = $menuButton->retrieveApiArray();

        $this->assertSame('view_miniprogram', $result['type']);
        $this->assertSame('小程序菜单', $result['name']);
        $this->assertSame('wx123456', $result['appid']);
        $this->assertSame('/pages/index/index', $result['pagepath']);
    }

    public function testRetrieveApiArrayThrowsExceptionWithoutType(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setName('测试菜单');

        $this->expectException(MenuButtonException::class);
        $this->expectExceptionMessage('按钮类型不能为空');

        $menuButton->retrieveApiArray();
    }

    public function testToString(): void
    {
        $menuButton = new MenuButton();
        $menuButton->setName('测试菜单');

        $this->assertSame('测试菜单', (string) $menuButton);
    }

    public function testFluentInterface(): void
    {
        $menuButton = new MenuButton();
        $agent = $this->createMock(AgentInterface::class);
        $corp = $this->createMock(CorpInterface::class);

        // 现在setter方法返回void，所以分别设置属性
        $menuButton->setName('测试菜单');
        $menuButton->setAgent($agent);
        $menuButton->setCorp($corp);
        $menuButton->setType(MenuButtonType::Click);
        $menuButton->setClickKey('test_key');
        $menuButton->setViewUrl('https://example.com');
        $menuButton->setMiniProgramAppId('wx123456');
        $menuButton->setMiniProgramPath('/pages/index/index');
        $menuButton->setSortNumber(10);
        $menuButton->setValid(true);

        // 验证属性已正确设置
        $this->assertSame('测试菜单', $menuButton->getName());
        $this->assertSame($agent, $menuButton->getAgent());
        $this->assertSame($corp, $menuButton->getCorp());
        $this->assertSame(MenuButtonType::Click, $menuButton->getType());
        $this->assertSame('test_key', $menuButton->getClickKey());
        $this->assertSame('https://example.com', $menuButton->getViewUrl());
        $this->assertSame('wx123456', $menuButton->getMiniProgramAppId());
        $this->assertSame('/pages/index/index', $menuButton->getMiniProgramPath());
        $this->assertSame(10, $menuButton->getSortNumber());
        $this->assertTrue($menuButton->isValid());
    }
}
