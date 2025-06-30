<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkMenuBundle\Exception\MenuButtonException;

class MenuButtonExceptionTest extends TestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new MenuButtonException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
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