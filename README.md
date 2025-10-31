# WechatWork Menu Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-menu-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-menu-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)  
[![PHP Version Require](http://poser.pugx.org/tourze/wechat-work-menu-bundle/require/php)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-work-menu-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-menu-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A Symfony bundle for managing WechatWork (Enterprise WeChat) application menus with support for hierarchical 
menu structures, multiple button types, and automatic API serialization.

## Table of Contents

- [Features](#features)
- [Dependencies](#dependencies)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Button Types](#button-types)
- [Repository Usage](#repository-usage)
- [Advanced Usage](#advanced-usage)
- [Configuration](#configuration)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Hierarchical Menu Structure**: Support for parent-child menu relationships
- **Multiple Button Types**: Click, View, ScanCode, Photo, Location, MiniProgram and more
- **Doctrine ORM Integration**: Entity management with repositories and relationships
- **API Serialization**: Automatic conversion to WechatWork API format
- **Symfony Integration**: Full Symfony bundle with services and controllers
- **Timestampable & Blameable**: Built-in tracking of creation and modification
- **Enum Support**: Type-safe button type definitions with labels

## Dependencies

This bundle requires:

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM
- `tourze/wechat-work-contracts` for interface definitions
- `tourze/doctrine-timestamp-bundle` for timestamp tracking
- `tourze/doctrine-user-bundle` for user tracking
- `tourze/enum-extra` for enhanced enum functionality

## Installation

```bash
composer require tourze/wechat-work-menu-bundle
```

## Quick Start

### 1. Register the Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\WechatWorkMenuBundle\WechatWorkMenuBundle::class => ['all' => true],
];
```

### 2. Create Menu Buttons

```php
<?php

use Tourze\WechatWorkMenuBundle\Entity\MenuButton;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;

// Create a parent menu button
$parentButton = new MenuButton();
$parentButton->setName('Main Menu');
$parentButton->setCorp($corp);
$parentButton->setAgent($agent);

// Create child buttons
$clickButton = new MenuButton();
$clickButton->setName('Click Me');
$clickButton->setType(MenuButtonType::Click);
$clickButton->setClickKey('click_key_1');
$clickButton->setParent($parentButton);

$viewButton = new MenuButton();
$viewButton->setName('Visit Website');
$viewButton->setType(MenuButtonType::View);
$viewButton->setViewUrl('https://example.com');
$viewButton->setParent($parentButton);

// Add to parent
$parentButton->addChild($clickButton);
$parentButton->addChild($viewButton);

// Save to database
$entityManager->persist($parentButton);
$entityManager->flush();
```

### 3. Generate API Array

```php
// Convert to WechatWork API format
$apiArray = $parentButton->retrieveApiArray();
// Returns:
// [
//     'name' => 'Main Menu',
//     'sub_button' => [
//         [
//             'type' => 'click',
//             'name' => 'Click Me',
//             'key' => 'click_key_1'
//         ],
//         [
//             'type' => 'view',
//             'name' => 'Visit Website',
//             'url' => 'https://example.com'
//         ]
//     ]
// ]
```

## Button Types

The bundle supports all WechatWork button types:

- **Click**: `MenuButtonType::Click` - Push event
- **View**: `MenuButtonType::View` - Jump to URL
- **ScanCodePush**: `MenuButtonType::ScanCodePush` - Scan QR code and push event
- **ScanCodeWaitMsg**: `MenuButtonType::ScanCodeWaitMsg` - Scan QR code with waiting message
- **PicSysPhoto**: `MenuButtonType::PicSysPhoto` - System camera photo
- **PicPhotoOrAlbum**: `MenuButtonType::PicPhotoOrAlbum` - Camera or album photo
- **PicWeixin**: `MenuButtonType::PicWeixin` - WeChat album photo
- **LocationSelect**: `MenuButtonType::LocationSelect` - Location selector
- **ViewMiniProgram**: `MenuButtonType::ViewMiniProgram` - Jump to mini program

## Repository Usage

```php
<?php

use Tourze\WechatWorkMenuBundle\Repository\MenuButtonRepository;

// Inject the repository
public function __construct(
    private MenuButtonRepository $menuButtonRepository
) {}

// Find buttons by corporation
$buttons = $this->menuButtonRepository->findBy(['corp' => $corp]);

// Find valid buttons with sorting
$validButtons = $this->menuButtonRepository->findBy(
    ['valid' => true], 
    ['sortNumber' => 'ASC']
);
```

## Advanced Usage

### Validation Constraints

The `MenuButton` entity includes comprehensive validation constraints:

```php
// All string fields have length constraints
$button->setName('Menu Name'); // Max 120 characters
$button->setClickKey('key123'); // Max 120 characters
$button->setViewUrl('https://example.com'); // Max 255 characters, must be valid URL
$button->setMiniProgramAppId('appid'); // Max 20 characters
$button->setMiniProgramPath('/pages/index'); // Max 255 characters

// Type must be valid enum value
$button->setType(MenuButtonType::Click);

// Sort number must be positive or zero
$button->setSortNumber(10);
```

### Custom Repository Methods

Extend the repository for custom queries:

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

### Hierarchical Menu Building

Build complex nested menus:

```php
// Create main menu structure
$mainMenu = new MenuButton();
$mainMenu->setName('企业应用');
$mainMenu->setCorp($corp);

// Services submenu
$servicesMenu = new MenuButton();
$servicesMenu->setName('服务中心');
$servicesMenu->setParent($mainMenu);

// Individual service buttons
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

## Configuration

The bundle uses standard Symfony configuration. Services are automatically registered via `services.yaml`.

## Testing

```bash
# Run tests
./vendor/bin/phpunit packages/wechat-work-menu-bundle/tests

# Run PHPStan analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-work-menu-bundle
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
