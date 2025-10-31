<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;
use Tourze\WechatWorkMenuBundle\Enum\MenuButtonType;

#[AdminCrud(routePath: '/wechat-work-menu/button', routeName: 'wechat_work_menu_button')]
final class MenuButtonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuButton::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('菜单按钮')
            ->setEntityLabelInPlural('菜单按钮')
            ->setPageTitle('index', '菜单按钮列表')
            ->setPageTitle('new', '新建菜单按钮')
            ->setPageTitle('edit', '编辑菜单按钮')
            ->setPageTitle('detail', '菜单按钮详情')
            ->setSearchFields(['name', 'clickKey', 'viewUrl', 'miniProgramAppId'])
            ->setDefaultSort(['sortNumber' => 'ASC', 'id' => 'DESC'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
                return $action->setLabel('查看')->setIcon('fas fa-eye');
            })
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'ID')
            ->onlyOnDetail()
        ;

        yield TextField::new('name', '标签名')
            ->setHelp('菜单按钮的显示名称，最长120个字符')
            ->setRequired(true)
        ;

        $typeField = EnumField::new('type', '按钮类型');
        $typeField->setEnumCases(MenuButtonType::cases());
        yield $typeField->setHelp('按钮的操作类型');

        yield TextField::new('clickKey', '点击键值')
            ->setHelp('click等点击类型必须，最长120个字符')
            ->hideOnIndex()
        ;

        yield UrlField::new('viewUrl', '网页链接')
            ->setHelp('view、view_miniprogram类型必须，最长255个字符')
            ->hideOnIndex()
        ;

        yield TextField::new('miniProgramAppId', '小程序AppId')
            ->setHelp('view_miniprogram类型必须，最长20个字符')
            ->hideOnIndex()
        ;

        yield TextField::new('miniProgramPath', '小程序路径')
            ->setHelp('view_miniprogram类型必须，最长255个字符')
            ->hideOnIndex()
        ;

        yield AssociationField::new('parent', '父级菜单')
            ->setHelp('如果是子菜单，选择其父级菜单')
            ->autocomplete()
        ;

        yield AssociationField::new('corp', '企业')
            ->setRequired(true)
            ->autocomplete()
        ;

        yield AssociationField::new('agent', '应用')
            ->setHelp('关联的企业微信应用')
            ->autocomplete()
        ;

        yield IntegerField::new('sortNumber', '排序')
            ->setHelp('数值越小越靠前')
            ->setColumns(6)
        ;

        yield BooleanField::new('valid', '有效')
            ->setHelp('是否启用此菜单按钮')
            ->setColumns(6)
        ;

        yield AssociationField::new('children', '子菜单')
            ->onlyOnDetail()
            ->setHelp('此菜单下的子菜单项')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('corp'))
            ->add(EntityFilter::new('agent'))
            ->add(EntityFilter::new('parent'))
            ->add('type')
            ->add(BooleanFilter::new('valid'))
        ;
    }
}
