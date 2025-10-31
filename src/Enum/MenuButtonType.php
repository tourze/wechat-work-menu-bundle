<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MenuButtonType: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case Click = 'click';
    case View = 'view';
    case ScanCodePush = 'scancode_push';
    case ScanCodeWaitMsg = 'scancode_waitmsg';
    case PicSysPhoto = 'pic_sysphoto';
    case PicPhotoOrAlbum = 'pic_photo_or_album';
    case PicWeixin = 'pic_weixin';
    case LocationSelect = 'location_select';
    case ViewMiniProgram = 'view_miniprogram';

    public function getLabel(): string
    {
        return match ($this) {
            self::Click => '点击推事件',
            self::View => '跳转URL',
            self::ScanCodePush => '扫码推事件',
            self::ScanCodeWaitMsg => '扫码推事件 且弹出"消息接收中"提示框',
            self::PicSysPhoto => '弹出系统拍照发图',
            self::PicPhotoOrAlbum => '弹出拍照或者相册发图',
            self::PicWeixin => '弹出企业微信相册发图器',
            self::LocationSelect => '弹出地理位置选择器',
            self::ViewMiniProgram => '跳转到小程序',
        };
    }

    public function getBadgeType(): string
    {
        return match ($this) {
            self::Click => 'primary',
            self::View => 'success',
            self::ScanCodePush, self::ScanCodeWaitMsg => 'info',
            self::PicSysPhoto, self::PicPhotoOrAlbum, self::PicWeixin => 'warning',
            self::LocationSelect => 'secondary',
            self::ViewMiniProgram => 'dark',
        };
    }

    public function getBadge(): string
    {
        return $this->getBadgeType();
    }
}
