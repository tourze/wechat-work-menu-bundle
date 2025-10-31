<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use Tourze\WechatWorkMenuBundle\Controller\AttachMenuController;

/**
 * @internal
 */
#[CoversClass(AttachMenuController::class)]
#[RunTestsInSeparateProcesses]
final class AttachMenuControllerTest extends AbstractWebTestCase
{
    public function testEnsureTestMethodNotAllowed(): void
    {
        $reflection = new \ReflectionClass(self::class);
        $method = $reflection->getMethod('testMethodNotAllowed');
        $attributes = $method->getAttributes(DataProvider::class);
        $this->assertCount(1, $attributes);

        $attribute = $attributes[0]->newInstance();
        $this->assertEquals('provideNotAllowedMethods', $attribute->methodName());
    }

    public function testControllerExists(): void
    {
        $controller = new AttachMenuController();

        $this->assertInstanceOf(AttachMenuController::class, $controller);
    }

    public function testControllerExtendsAbstractController(): void
    {
        $controller = new AttachMenuController();

        $this->assertInstanceOf(AbstractController::class, $controller);
    }

    public function testInvokeMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/wechat-work/menu/attach');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);
        $this->assertJson($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertEquals('Wechat Work Menu Attach Controller', $data['message']);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request($method, '/wechat-work/menu/attach');
    }
}
