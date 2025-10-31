<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;

/**
 * 企业微信菜单管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
final readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('企业微信')) {
            $item->addChild('企业微信')
                ->setAttribute('icon', 'fab fa-weixin')
            ;
        }

        $wechatMenu = $item->getChild('企业微信');
        if (null === $wechatMenu) {
            return;
        }

        // 添加菜单管理子菜单
        if (null === $wechatMenu->getChild('菜单管理')) {
            $wechatMenu->addChild('菜单管理')
                ->setAttribute('icon', 'fas fa-bars')
            ;
        }

        $menuManagement = $wechatMenu->getChild('菜单管理');
        if (null === $menuManagement) {
            return;
        }

        $menuManagement->addChild('菜单按钮')
            ->setUri($this->linkGenerator->getCurdListPage(MenuButton::class))
            ->setAttribute('icon', 'fas fa-mouse-pointer')
        ;
    }
}
