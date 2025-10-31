<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\WechatWorkMenuBundle\Exception\MenuButtonException;

/**
 * @internal
 */
#[CoversClass(MenuButtonException::class)]
final class MenuButtonExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionBehavesAsRuntimeException(): void
    {
        $exception = new MenuButtonException();
        // 验证继承关系以及实际异常行为
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);

        // 验证异常的基本功能
        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Menu button error';
        $exception = new MenuButtonException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithCode(): void
    {
        $code = 123;
        $exception = new MenuButtonException('Error', $code);
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new MenuButtonException('Current error', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
