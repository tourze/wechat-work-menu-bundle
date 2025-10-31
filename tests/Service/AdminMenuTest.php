<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\WechatWorkMenuBundle\Service\AdminMenu;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        if (is_callable($adminMenu)) {
            $adminMenu($rootItem);
        }

        // 验证菜单结构
        $wechatMenu = $rootItem->getChild('企业微信');
        self::assertNotNull($wechatMenu);

        $menuManagement = $wechatMenu->getChild('菜单管理');
        self::assertNotNull($menuManagement);

        self::assertNotNull($menuManagement->getChild('菜单按钮'));
    }

    public function testClassIsFinal(): void
    {
        $reflection = new \ReflectionClass(AdminMenu::class);
        self::assertTrue($reflection->isFinal(), 'AdminMenu class should be final');
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);

        self::assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }
}
