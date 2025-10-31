<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\WechatWorkMenuBundle\WechatWorkMenuBundle;

/**
 * @internal
 */
#[CoversClass(WechatWorkMenuBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatWorkMenuBundleTest extends AbstractBundleTestCase
{
}
