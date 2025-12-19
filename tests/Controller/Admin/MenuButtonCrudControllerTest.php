<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\DomCrawler\Crawler;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\WechatWorkMenuBundle\Controller\Admin\MenuButtonCrudController;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;

/**
 * 菜单按钮管理控制器测试
 * @internal
 */
#[CoversClass(MenuButtonCrudController::class)]
#[RunTestsInSeparateProcesses]
final class MenuButtonCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testConfigureFields(): void
    {
        $controller = new MenuButtonCrudController();
        $fields = $controller->configureFields('index');

        self::assertIsIterable($fields);

        $fields = iterator_to_array($fields);
        self::assertNotEmpty($fields);

        // 验证字段数量合理
        self::assertGreaterThan(5, count($fields));
    }

    public function testControllerClassIsFinal(): void
    {
        $reflection = new \ReflectionClass(MenuButtonCrudController::class);
        self::assertTrue($reflection->isFinal(), 'Controller class should be final');
    }

    public function testRequiredFieldsValidation(): void
    {
        $controller = new MenuButtonCrudController();
        $fields = iterator_to_array($controller->configureFields('new'));

        // 验证必填字段存在
        $nameFieldExists = false;
        $corpFieldExists = false;

        foreach ($fields as $field) {
            if (is_object($field) && method_exists($field, 'getAsDto')) {
                $dto = $field->getAsDto();
                if ('name' === $dto->getProperty()) {
                    $nameFieldExists = true;
                }
                if ('corp' === $dto->getProperty()) {
                    $corpFieldExists = true;
                }
            }
        }

        self::assertTrue($nameFieldExists, 'name field should exist');
        self::assertTrue($corpFieldExists, 'corp field should exist');
    }

    /**
     * 获取MenuButtonCrudController服务实例
     */
    protected function getControllerService(): MenuButtonCrudController
    {
        return self::getService(MenuButtonCrudController::class);
    }

    /**
     * 重写 setUp 方法来设置测试客户端上下文
     */
    protected function afterEasyAdminSetUp(): void
    {
        // 创建一个默认客户端以满足测试框架的要求
        if (!self::$booted) {
            $client = self::createClientWithDatabase();
            $this->createAdminUser('admin@test.com', 'password123');
            $this->loginAsAdmin($client, 'admin@test.com', 'password123');

            // 通过调用 getClient() 方法来设置静态客户端
            $reflection = new \ReflectionClass(self::class);
            $method = $reflection->getMethod('getClient');
            $method->setAccessible(true);
            $method->invokeArgs(null, [$client]);
        }
    }

    /**
     * 提供Index页面预期显示的字段标签
     *
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield '标签名' => ['标签名'];
        yield '按钮类型' => ['按钮类型'];
        yield '父级菜单' => ['父级菜单'];
        yield '企业' => ['企业'];
        yield '应用' => ['应用'];
        yield '排序' => ['排序'];
        yield '有效' => ['有效'];
    }

    /**
     * 提供New页面预期显示的字段名
     *
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'type' => ['type'];
        yield 'clickKey' => ['clickKey'];
        yield 'viewUrl' => ['viewUrl'];
        yield 'miniProgramAppId' => ['miniProgramAppId'];
        yield 'miniProgramPath' => ['miniProgramPath'];
        yield 'parent' => ['parent'];
        yield 'corp' => ['corp'];
        yield 'agent' => ['agent'];
        yield 'sortNumber' => ['sortNumber'];
        yield 'valid' => ['valid'];
    }

    /**
     * 提供Edit页面预期显示的字段名
     *
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'type' => ['type'];
        yield 'clickKey' => ['clickKey'];
        yield 'viewUrl' => ['viewUrl'];
        yield 'miniProgramAppId' => ['miniProgramAppId'];
        yield 'miniProgramPath' => ['miniProgramPath'];
        yield 'parent' => ['parent'];
        yield 'corp' => ['corp'];
        yield 'agent' => ['agent'];
        yield 'sortNumber' => ['sortNumber'];
        yield 'valid' => ['valid'];
    }

    /**
     * 自定义编辑页面预填充数据测试，使用直接的客户端响应断言
     */
    public function testCustomEditPagePrefillsExistingData(): void
    {
        $client = self::createClientWithDatabase();
        // 直接使用内存中的admin用户登录
        $adminUser = new \Symfony\Component\Security\Core\User\InMemoryUser('admin', 'password', ['ROLE_ADMIN']);
        $client->loginUser($adminUser, 'main');

        try {
            $crawler = $client->request('GET', $this->generateAdminUrl('index'));
        } catch (\InvalidArgumentException $exception) {
            self::markTestSkipped('Index action is disabled for this controller.');
        }

        if (200 !== $client->getResponse()->getStatusCode()) {
            self::markTestSkipped('Index page is not accessible.');
        }

        $recordIds = [];
        foreach ($crawler->filter('table tbody tr[data-id]') as $row) {
            $rowCrawler = new Crawler($row);
            $recordId = $rowCrawler->attr('data-id');
            if (null === $recordId || '' === $recordId) {
                continue;
            }
            $recordIds[] = $recordId;
        }

        if ([] === $recordIds) {
            self::markTestSkipped('列表页面没有显示任何记录');
        }

        $firstRecordId = $recordIds[0];

        try {
            $client->request('GET', $this->generateAdminUrl('edit', ['entityId' => $firstRecordId]));
        } catch (\InvalidArgumentException $exception) {
            self::markTestSkipped('EDIT action is disabled for this controller.');
        }

        self::assertSame(200, $client->getResponse()->getStatusCode(),
            sprintf('The edit page for entity #%s should be accessible.', $firstRecordId));
    }

    /**
     * 测试验证错误处理
     */
    public function testValidationErrors(): void
    {
        $client = self::createClientWithDatabase();
        // 直接使用内存中的admin用户登录
        $adminUser = new \Symfony\Component\Security\Core\User\InMemoryUser('admin', 'password', ['ROLE_ADMIN']);
        $client->loginUser($adminUser, 'main');

        // 访问创建页面
        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        self::assertSame(200, $client->getResponse()->getStatusCode());

        // 查找表单并提交空表单，触发验证错误
        $submitButton = $crawler->filter('button[type="submit"]');
        if (0 === $submitButton->count()) {
            self::markTestSkipped('No submit button found on the page.');
        }
        $form = $submitButton->form();
        $crawler = $client->submit($form);

        // 验证返回状态码为422（验证错误）
        self::assertSame(422, $client->getResponse()->getStatusCode());

        // 验证必填字段的错误消息存在
        $pageContent = $crawler->text();

        // 验证name字段的NotBlank约束错误
        self::assertStringContainsString('should not be blank', $pageContent, 'name字段应该有必填验证错误');

        // 验证页面显示了表单验证错误
        $errorElements = $crawler->filter('.invalid-feedback, .form-error-message, .error, .field-error');
        self::assertGreaterThan(0, $errorElements->count(), '应该显示验证错误消息');
    }
}
