# 企业微信菜单包

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-menu-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-menu-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)  
[![PHP Version Require](http://poser.pugx.org/tourze/wechat-work-menu-bundle/require/php)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-menu-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

用于管理企业微信应用菜单的 Symfony Bundle，支持层次化菜单结构、多种按钮类型和自动 API 序列化。

## 目录

- [功能特性](#功能特性)
- [依赖要求](#依赖要求)
- [安装](#安装)
- [快速开始](#快速开始)
- [按钮类型](#按钮类型)
- [仓储使用](#仓储使用)
- [高级用法](#高级用法)
- [配置](#配置)
- [测试](#测试)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **层次化菜单结构**: 支持父子菜单关系管理
- **多种按钮类型**: 点击、跳转、扫码、拍照、定位、小程序等多种类型
- **Doctrine ORM 集成**: 实体管理，包含仓储和关联关系
- **API 序列化**: 自动转换为企业微信 API 格式
- **Symfony 集成**: 完整的 Symfony Bundle，包含服务和控制器
- **时间戳追踪**: 内置创建和修改记录追踪
- **枚举支持**: 类型安全的按钮类型定义，带有标签

## 依赖要求

此 Bundle 需要：

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM
- `tourze/wechat-work-contracts` 用于接口定义
- `tourze/doctrine-timestamp-bundle` 用于时间戳追踪
- `tourze/doctrine-user-bundle` 用于用户追踪
- `tourze/enum-extra` 用于增强枚举功能

## 安装

```bash
composer require tourze/wechat-work-menu-bundle
```

## 快速开始

### 1. 注册 Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\WechatWorkMenuBundle\WechatWorkMenuBundle::class => ['all' => true],
];
```

### 2. 创建菜单按钮

```php
<?php

use Tourze\WechatWorkMenuBundle\Entity\MenuButton;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;

// 创建父菜单按钮
$parentButton = new MenuButton();
$parentButton->setName('主菜单');
$parentButton->setCorp($corp);
$parentButton->setAgent($agent);

// 创建子按钮
$clickButton = new MenuButton();
$clickButton->setName('点击按钮');
$clickButton->setType(MenuButtonType::Click);
$clickButton->setClickKey('click_key_1');
$clickButton->setParent($parentButton);

$viewButton = new MenuButton();
$viewButton->setName('访问网站');
$viewButton->setType(MenuButtonType::View);
$viewButton->setViewUrl('https://example.com');
$viewButton->setParent($parentButton);

// 添加到父按钮
$parentButton->addChild($clickButton);
$parentButton->addChild($viewButton);

// 保存到数据库
$entityManager->persist($parentButton);
$entityManager->flush();
```

### 3. 生成 API 数组

```php
// 转换为企业微信 API 格式
$apiArray = $parentButton->retrieveApiArray();
// 返回:
// [
//     'name' => '主菜单',
//     'sub_button' => [
//         [
//             'type' => 'click',
//             'name' => '点击按钮',
//             'key' => 'click_key_1'
//         ],
//         [
//             'type' => 'view',
//             'name' => '访问网站',
//             'url' => 'https://example.com'
//         ]
//     ]
// ]
```

## 按钮类型

Bundle 支持所有企业微信按钮类型：

- **Click**: `MenuButtonType::Click` - 点击推事件
- **View**: `MenuButtonType::View` - 跳转URL
- **ScanCodePush**: `MenuButtonType::ScanCodePush` - 扫码推事件
- **ScanCodeWaitMsg**: `MenuButtonType::ScanCodeWaitMsg` - 扫码推事件且弹出"消息接收中"提示框
- **PicSysPhoto**: `MenuButtonType::PicSysPhoto` - 弹出系统拍照发图
- **PicPhotoOrAlbum**: `MenuButtonType::PicPhotoOrAlbum` - 弹出拍照或者相册发图
- **PicWeixin**: `MenuButtonType::PicWeixin` - 弹出企业微信相册发图器
- **LocationSelect**: `MenuButtonType::LocationSelect` - 弹出地理位置选择器
- **ViewMiniProgram**: `MenuButtonType::ViewMiniProgram` - 跳转到小程序

## 仓储使用

```php
<?php

use Tourze\WechatWorkMenuBundle\Repository\MenuButtonRepository;

// 注入仓储
public function __construct(
    private MenuButtonRepository $menuButtonRepository
) {}

// 按企业查找按钮
$buttons = $this->menuButtonRepository->findBy(['corp' => $corp]);

// 查找有效按钮并排序
$validButtons = $this->menuButtonRepository->findBy(
    ['valid' => true], 
    ['sortNumber' => 'ASC']
);
```

## 高级用法

### 验证约束

`MenuButton` 实体包含完整的验证约束：

```php
// 所有字符串字段都有长度约束
$button->setName('菜单名称'); // 最大 120 字符
$button->setClickKey('key123'); // 最大 120 字符
$button->setViewUrl('https://example.com'); // 最大 255 字符，必须是有效 URL
$button->setMiniProgramAppId('appid'); // 最大 20 字符
$button->setMiniProgramPath('/pages/index'); // 最大 255 字符

// 类型必须是有效的枚举值
$button->setType(MenuButtonType::Click);

// 排序号必须是正数或零
$button->setSortNumber(10);
```

### 自定义仓储方法

扩展仓储以支持自定义查询：

```php
<?php

namespace App\Repository;

use Tourze\WechatWorkMenuBundle\Repository\MenuButtonRepository as BaseRepository;

class MenuButtonRepository extends BaseRepository
{
    public function findRootMenus()
    {
        return $this->createQueryBuilder('m')
            ->where('m.parent IS NULL')
            ->andWhere('m.valid = :valid')
            ->setParameter('valid', true)
            ->orderBy('m.sortNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
```

### 层次化菜单构建

构建复杂的嵌套菜单：

```php
// 创建主菜单结构
$mainMenu = new MenuButton();
$mainMenu->setName('企业应用');
$mainMenu->setCorp($corp);

// 服务子菜单
$servicesMenu = new MenuButton();
$servicesMenu->setName('服务中心');
$servicesMenu->setParent($mainMenu);

// 具体服务按钮
$serviceButtons = [
    ['name' => '在线客服', 'key' => 'customer_service'],
    ['name' => '产品咨询', 'key' => 'product_inquiry'],
    ['name' => '技术支持', 'key' => 'tech_support'],
];

foreach ($serviceButtons as $index => $buttonData) {
    $button = new MenuButton();
    $button->setName($buttonData['name']);
    $button->setType(MenuButtonType::Click);
    $button->setClickKey($buttonData['key']);
    $button->setParent($servicesMenu);
    $button->setSortNumber($index);
    $servicesMenu->addChild($button);
}

$mainMenu->addChild($servicesMenu);
```

## 配置

Bundle 使用标准的 Symfony 配置。服务通过 `services.yaml` 自动注册。

## 测试

```bash
# 运行测试
./vendor/bin/phpunit packages/wechat-work-menu-bundle/tests

# 运行 PHPStan 分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-work-menu-bundle
```

## 贡献

1. Fork 仓库
2. 创建功能分支
3. 进行修改
4. 为新功能添加测试
5. 确保所有测试通过
6. 提交 Pull Request

## 许可证

MIT 许可证。详情请参阅 [License File](LICENSE)。
