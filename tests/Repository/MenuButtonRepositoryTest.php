<?php

namespace Tourze\WechatWorkMenuBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;
use Tourze\WechatWorkMenuBundle\Repository\MenuButtonRepository;
use WechatWorkBundle\Entity\Corp;

/**
 * @internal
 */
#[CoversClass(MenuButtonRepository::class)]
#[RunTestsInSeparateProcesses]
final class MenuButtonRepositoryTest extends AbstractRepositoryTestCase
{
    private MenuButtonRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(MenuButtonRepository::class);
    }

    private function createTestCorp(): Corp
    {
        $corp = new Corp();
        $corp->setCorpId('test_corp_' . uniqid());
        $corp->setName('Test Corp ' . uniqid());
        $corp->setCorpSecret('test_secret');
        self::getEntityManager()->persist($corp);

        return $corp;
    }

    public function testSaveAndFlush(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Test Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('test_key');
        $button->setCorp($corp);

        $this->repository->save($button);

        $this->assertNotNull($button->getId());
        $this->assertSame('Test Button', $button->getName());
    }

    public function testSaveWithoutFlush(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Test Button No Flush');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('test_key_no_flush');
        $button->setCorp($corp);

        $this->repository->save($button, false);

        $this->assertNull($button->getId());
    }

    public function testRemoveAndFlush(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Button to Remove');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('remove_key');
        $button->setCorp($corp);

        $this->repository->save($button);
        $id = $button->getId();

        $this->repository->remove($button);

        $removedButton = $this->repository->find($id);
        $this->assertNull($removedButton);
    }

    public function testQueryByNullableField(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Nullable Test Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('nullable_key');
        $button->setViewUrl(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['viewUrl' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableField(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Nullable Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('count_nullable_key');
        $button->setMiniProgramAppId(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['miniProgramAppId' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByAssociation(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent Button');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_key');
        $parentButton->setCorp($corp);

        $childButton = new MenuButton();
        $childButton->setName('Child Button');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_key');
        $childButton->setParent($parentButton);
        $childButton->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $children = $this->repository->findBy(['parent' => $parentButton]);
        $this->assertIsArray($children);
        $this->assertGreaterThanOrEqual(1, count($children));
    }

    public function testCountByAssociation(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent for Count');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_count_key');
        $parentButton->setCorp($corp);

        $childButton = new MenuButton();
        $childButton->setName('Child for Count');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_count_key');
        $childButton->setParent($parentButton);
        $childButton->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $count = $this->repository->count(['parent' => $parentButton]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderBy(): void
    {
        $corp = $this->createTestCorp();

        $button1 = new MenuButton();
        $button1->setName('Z Button');
        $button1->setType(MenuButtonType::Click);
        $button1->setClickKey('z_key');
        $button1->setCorp($corp);

        $button2 = new MenuButton();
        $button2->setName('A Button');
        $button2->setType(MenuButtonType::Click);
        $button2->setClickKey('a_key');
        $button2->setCorp($corp);

        $this->repository->save($button1);
        $this->repository->save($button2);

        $button = $this->repository->findOneBy(['type' => MenuButtonType::Click], ['name' => 'ASC']);
        $this->assertInstanceOf(MenuButton::class, $button);
        $this->assertSame('A Button', $button->getName());
    }

    public function testFindOneByWithInvalidField(): void
    {
        $this->expectException(\Exception::class);
        $this->repository->findOneBy(['invalidField' => 'value']);
    }

    public function testQueryByNullableClickKey(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null ClickKey Button');
        $button->setType(MenuButtonType::View);
        $button->setViewUrl('https://example.com');
        $button->setClickKey(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['clickKey' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableClickKey(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null ClickKey');
        $button->setType(MenuButtonType::View);
        $button->setViewUrl('https://example.com');
        $button->setClickKey(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['clickKey' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByAgentAssociation(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Agent Test Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('agent_key');
        $button->setAgent(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['agent' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByAgentAssociation(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Agent Count Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('agent_count_key');
        $button->setAgent(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['agent' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithSortingLogic(): void
    {
        $corp = $this->createTestCorp();

        $button1 = new MenuButton();
        $button1->setName('Button C');
        $button1->setType(MenuButtonType::Click);
        $button1->setClickKey('c_key');
        $button1->setSortNumber(100);
        $button1->setCorp($corp);

        $button2 = new MenuButton();
        $button2->setName('Button A');
        $button2->setType(MenuButtonType::Click);
        $button2->setClickKey('a_key');
        $button2->setSortNumber(50);
        $button2->setCorp($corp);

        $this->repository->save($button1);
        $this->repository->save($button2);

        // 使用特定的 corp 来限定查询范围，避免与 DataFixtures 数据冲突
        $button = $this->repository->findOneBy(['type' => MenuButtonType::Click, 'corp' => $corp], ['sortNumber' => 'ASC']);
        $this->assertInstanceOf(MenuButton::class, $button);
        $this->assertSame(50, $button->getSortNumber());
    }

    public function testQueryByChildrenAssociation(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent with Children');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_children_key');
        $parentButton->setCorp($corp);

        $childButton = new MenuButton();
        $childButton->setName('Child Button');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_button_key');
        $childButton->setParent($parentButton);
        $childButton->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $children = $this->repository->findBy(['parent' => $parentButton]);
        $this->assertIsArray($children);
        $this->assertGreaterThanOrEqual(1, count($children));
    }

    public function testCountByChildrenAssociation(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent for Count Children');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_count_children_key');
        $parentButton->setCorp($corp);

        $childButton = new MenuButton();
        $childButton->setName('Child for Count');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_for_count_key');
        $childButton->setParent($parentButton);
        $childButton->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $count = $this->repository->count(['parent' => $parentButton]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByNullableViewUrl(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null ViewUrl Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('null_viewurl_key');
        $button->setViewUrl(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['viewUrl' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableViewUrl(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null ViewUrl');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('count_null_viewurl_key');
        $button->setViewUrl(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['viewUrl' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByNullableMiniProgramAppId(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null MiniProgram Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('null_miniprogram_key');
        $button->setMiniProgramAppId(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['miniProgramAppId' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableMiniProgramAppId(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null MiniProgram');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('count_null_miniprogram_key');
        $button->setMiniProgramAppId(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['miniProgramAppId' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByNullableMiniProgramPath(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null MiniProgram Path Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('null_miniprogram_path_key');
        $button->setMiniProgramPath(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['miniProgramPath' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableMiniProgramPath(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null MiniProgram Path');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('count_null_miniprogram_path_key');
        $button->setMiniProgramPath(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['miniProgramPath' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByNullableType(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null Type Button');
        $button->setClickKey('null_type_key');
        $button->setType(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['type' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableType(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null Type');
        $button->setClickKey('count_null_type_key');
        $button->setType(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['type' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByNullableSortNumber(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null SortNumber Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('null_sort_key');
        $button->setSortNumber(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['sortNumber' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableSortNumber(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null SortNumber');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('count_null_sort_key');
        $button->setSortNumber(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['sortNumber' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByNullableValid(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Null Valid Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('null_valid_key');
        $button->setValid(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['valid' => null]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByNullableValid(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Count Null Valid');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('count_null_valid_key');
        $button->setValid(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['valid' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testQueryByCorpAssociation(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Corp Association Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('corp_assoc_key');
        $button->setCorp($corp);

        $this->repository->save($button);

        $buttons = $this->repository->findBy(['corp' => $corp]);
        $this->assertIsArray($buttons);
        $this->assertGreaterThanOrEqual(1, count($buttons));
    }

    public function testCountByCorpAssociation(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Corp Count Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('corp_count_key');
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByAssociationCorpShouldReturnMatchingEntity(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Corp Association Test');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('corp_assoc_test_key');
        $button->setCorp($corp);

        $this->repository->save($button);

        $result = $this->repository->findOneBy(['corp' => $corp]);
        $this->assertInstanceOf(MenuButton::class, $result);
        $this->assertSame($corp, $result->getCorp());
    }

    public function testFindOneByAssociationAgentShouldReturnMatchingEntity(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Agent Association Test');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('agent_assoc_test_key');
        $button->setAgent(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $result = $this->repository->findOneBy(['agent' => null]);
        $this->assertInstanceOf(MenuButton::class, $result);
        $this->assertNull($result->getAgent());
    }

    public function testFindOneByAssociationParentShouldReturnMatchingEntity(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent for Association Test');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_assoc_test_key');
        $parentButton->setCorp($corp);

        $childButton = new MenuButton();
        $childButton->setName('Child for Association Test');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_assoc_test_key');
        $childButton->setParent($parentButton);
        $childButton->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $result = $this->repository->findOneBy(['parent' => $parentButton]);
        $this->assertInstanceOf(MenuButton::class, $result);
        $this->assertSame($parentButton, $result->getParent());
    }

    public function testCountByAssociationCorpShouldReturnCorrectNumber(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Corp Count Test');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('corp_count_test_key');
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['corp' => $corp]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountByAssociationAgentShouldReturnCorrectNumber(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Agent Count Test');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('agent_count_test_key');
        $button->setAgent(null);
        $button->setCorp($corp);

        $this->repository->save($button);

        $count = $this->repository->count(['agent' => null]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountByAssociationParentShouldReturnCorrectNumber(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent for Count Test');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_count_test_key');
        $parentButton->setCorp($corp);

        $childButton = new MenuButton();
        $childButton->setName('Child for Count Test');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_count_test_key');
        $childButton->setParent($parentButton);
        $childButton->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $count = $this->repository->count(['parent' => $parentButton]);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindRootButtons(): void
    {
        $corp = $this->createTestCorp();

        $rootButton = new MenuButton();
        $rootButton->setName('Root Button');
        $rootButton->setType(MenuButtonType::Click);
        $rootButton->setClickKey('root_key');
        $rootButton->setCorp($corp);
        $rootButton->setValid(true);
        $rootButton->setSortNumber(1);

        $childButton = new MenuButton();
        $childButton->setName('Child Button');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_key');
        $childButton->setCorp($corp);
        $childButton->setValid(true);
        $childButton->setSortNumber(2);

        $this->repository->save($rootButton);
        $this->repository->save($childButton);

        $childButton->setParent($rootButton);
        $this->repository->save($childButton);

        $rootButtons = $this->repository->findRootButtons();

        $this->assertIsArray($rootButtons);
        $this->assertGreaterThan(0, count($rootButtons));

        $foundRoot = null;
        foreach ($rootButtons as $button) {
            if ('Root Button' === $button->getName()) {
                $foundRoot = $button;
                break;
            }
        }
        $this->assertNotNull($foundRoot);
        $this->assertNull($foundRoot->getParent());
    }

    public function testFindChildButtons(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent Button');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_key');
        $parentButton->setCorp($corp);
        $parentButton->setValid(true);

        $childButton = new MenuButton();
        $childButton->setName('Child Button');
        $childButton->setType(MenuButtonType::Click);
        $childButton->setClickKey('child_key');
        $childButton->setCorp($corp);
        $childButton->setValid(true);

        $this->repository->save($parentButton);
        $this->repository->save($childButton);

        $childButton->setParent($parentButton);
        $this->repository->save($childButton);

        $childButtons = $this->repository->findChildButtons($parentButton);

        $this->assertIsArray($childButtons);
        $this->assertCount(1, $childButtons);
        $this->assertSame('Child Button', $childButtons[0]->getName());
    }

    public function testFindValidButtons(): void
    {
        $corp = $this->createTestCorp();

        $validButton = new MenuButton();
        $validButton->setName('Valid Button');
        $validButton->setType(MenuButtonType::Click);
        $validButton->setClickKey('valid_key');
        $validButton->setCorp($corp);
        $validButton->setValid(true);

        $invalidButton = new MenuButton();
        $invalidButton->setName('Invalid Button');
        $invalidButton->setType(MenuButtonType::Click);
        $invalidButton->setClickKey('invalid_key');
        $invalidButton->setCorp($corp);
        $invalidButton->setValid(false);

        $this->repository->save($validButton);
        $this->repository->save($invalidButton);

        $validButtons = $this->repository->findValidButtons();

        $this->assertIsArray($validButtons);
        $foundValid = false;
        $foundInvalid = false;

        foreach ($validButtons as $button) {
            if ('Valid Button' === $button->getName()) {
                $foundValid = true;
            }
            if ('Invalid Button' === $button->getName()) {
                $foundInvalid = true;
            }
        }

        $this->assertTrue($foundValid);
        $this->assertFalse($foundInvalid);
    }

    public function testCountChildButtons(): void
    {
        $corp = $this->createTestCorp();

        $parentButton = new MenuButton();
        $parentButton->setName('Parent Button');
        $parentButton->setType(MenuButtonType::Click);
        $parentButton->setClickKey('parent_key');
        $parentButton->setCorp($corp);

        $childButton1 = new MenuButton();
        $childButton1->setName('Child Button 1');
        $childButton1->setType(MenuButtonType::Click);
        $childButton1->setClickKey('child_key_1');
        $childButton1->setCorp($corp);

        $childButton2 = new MenuButton();
        $childButton2->setName('Child Button 2');
        $childButton2->setType(MenuButtonType::Click);
        $childButton2->setClickKey('child_key_2');
        $childButton2->setCorp($corp);

        $this->repository->save($parentButton);
        $this->repository->save($childButton1);
        $this->repository->save($childButton2);

        $childButton1->setParent($parentButton);
        $childButton2->setParent($parentButton);
        $this->repository->save($childButton1);
        $this->repository->save($childButton2);

        $count = $this->repository->countChildButtons($parentButton);

        $this->assertSame(2, $count);
    }

    public function testFindByClickKey(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Button with Click Key');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('unique_click_key');
        $button->setCorp($corp);
        $button->setValid(true);

        $this->repository->save($button);

        $foundButton = $this->repository->findByClickKey('unique_click_key');

        $this->assertInstanceOf(MenuButton::class, $foundButton);
        $this->assertSame('Button with Click Key', $foundButton->getName());
        $this->assertSame('unique_click_key', $foundButton->getClickKey());
    }

    public function testFindByClickKeyReturnsNullForInvalidButton(): void
    {
        $corp = $this->createTestCorp();

        $button = new MenuButton();
        $button->setName('Invalid Button');
        $button->setType(MenuButtonType::Click);
        $button->setClickKey('invalid_button_key');
        $button->setCorp($corp);
        $button->setValid(false);

        $this->repository->save($button);

        $foundButton = $this->repository->findByClickKey('invalid_button_key');

        $this->assertNull($foundButton);
    }

    protected function createNewEntity(): object
    {
        $entity = new MenuButton();

        // 设置基本字段
        $entity->setName('Test MenuButton ' . uniqid());
        $entity->setValid(true);
        $entity->setSortNumber(0);

        // 设置必需的 Corp
        $corp = new Corp();
        $corp->setCorpId('wxcorp' . uniqid());
        $corp->setName('Test Corp ' . uniqid());
        $corp->setCorpSecret('test_secret');
        self::getEntityManager()->persist($corp);
        $entity->setCorp($corp);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<MenuButton>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
