<?php

declare(strict_types=1);

namespace Tourze\WechatWorkMenuBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\WechatWorkMenuBundle\Entity\MenuButton;

/**
 * @extends ServiceEntityRepository<MenuButton>
 */
#[AsRepository(entityClass: MenuButton::class)]
final class MenuButtonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuButton::class);
    }

    public function save(MenuButton $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MenuButton $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<MenuButton>
     */
    public function findRootButtons(): array
    {
        /** @var array<MenuButton> $result */
        $result = $this->createQueryBuilder('m')
            ->where('m.parent IS NULL')
            ->andWhere('m.valid = true')
            ->orderBy('m.sortNumber', 'ASC')
            ->addOrderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @return array<MenuButton>
     */
    public function findChildButtons(MenuButton $parent): array
    {
        /** @var array<MenuButton> $result */
        $result = $this->createQueryBuilder('m')
            ->where('m.parent = :parent')
            ->andWhere('m.valid = true')
            ->setParameter('parent', $parent)
            ->orderBy('m.sortNumber', 'ASC')
            ->addOrderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    /**
     * @return array<MenuButton>
     */
    public function findValidButtons(): array
    {
        /** @var array<MenuButton> $result */
        $result = $this->createQueryBuilder('m')
            ->where('m.valid = true')
            ->orderBy('m.sortNumber', 'ASC')
            ->addOrderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    public function countChildButtons(MenuButton $parent): int
    {
        $result = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.parent = :parent')
            ->setParameter('parent', $parent)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $result;
    }

    public function findByClickKey(string $clickKey): ?MenuButton
    {
        /** @var MenuButton|null $result */
        $result = $this->createQueryBuilder('m')
            ->where('m.clickKey = :clickKey')
            ->andWhere('m.valid = true')
            ->setParameter('clickKey', $clickKey)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }
}
