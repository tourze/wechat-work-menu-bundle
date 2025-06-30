<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkMenuBundle\Repository\MenuButtonRepository;

class MenuButtonRepositoryTest extends TestCase
{
    public function testRepositoryClass_extendsServiceEntityRepository(): void
    {
        // 使用反射检查类的继承关系
        $repositoryClass = new \ReflectionClass(MenuButtonRepository::class);
        $this->assertTrue($repositoryClass->isSubclassOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository'));
    }
    
    public function testConstructor_acceptsManagerRegistry(): void
    {
        // 检查构造函数参数
        $constructor = (new \ReflectionClass(MenuButtonRepository::class))->getConstructor();
        $parameters = $constructor->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('registry', $parameters[0]->getName());
        $type = $parameters[0]->getType();
        $this->assertInstanceOf(\ReflectionNamedType::class, $type);
        $this->assertSame(ManagerRegistry::class, $type->getName());
    }
    
    public function testConstructor_passesCorrectEntityClass(): void
    {
        // 获取构造函数代码
        $constructor = (new \ReflectionClass(MenuButtonRepository::class))->getConstructor();
        $fileContent = file_get_contents((new \ReflectionClass(MenuButtonRepository::class))->getFileName());
        
        // 检查代码中是否包含传递MenuButton::class给父类构造函数
        $pattern = '/parent::__construct\(\$registry,\s*MenuButton::class\)/';
        $this->assertMatchesRegularExpression($pattern, $fileContent);
    }
    
    public function testEntityUsage_inPhpDoc(): void
    {
        // 检查类文档注释
        $reflectionClass = new \ReflectionClass(MenuButtonRepository::class);
        $docComment = $reflectionClass->getDocComment();
        
        // 应该包含正确的方法文档
        $this->assertStringContainsString('@method MenuButton|null find', $docComment);
        $this->assertStringContainsString('@method MenuButton|null findOneBy', $docComment);
        $this->assertStringContainsString('@method MenuButton[]', $docComment);
    }
    
    public function testRepositoryStructure_noCustomMethods(): void
    {
        // 获取所有公共方法，除了继承来的方法
        $repositoryMethods = array_filter(
            (new \ReflectionClass(MenuButtonRepository::class))->getMethods(\ReflectionMethod::IS_PUBLIC),
            function (\ReflectionMethod $method) {
                return $method->getDeclaringClass()->getName() === MenuButtonRepository::class;
            }
        );
        
        // 应该只有构造函数，没有自定义方法
        $this->assertCount(1, $repositoryMethods);
        $this->assertSame('__construct', $repositoryMethods[0]->getName());
    }
} 