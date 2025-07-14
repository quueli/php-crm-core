<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findRootCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->orderBy('c.sortOrder', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByParent(?Category $parent): array
    {
        $qb = $this->createQueryBuilder('c');

        if ($parent === null) {
            $qb->where('c.parent IS NULL');
        } else {
            $qb->where('c.parent = :parent')
               ->setParameter('parent', $parent);
        }

        return $qb->orderBy('c.sortOrder', 'ASC')
                  ->addOrderBy('c.name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    public function findByLevel(int $level): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.level = :level')
            ->setParameter('level', $level)
            ->orderBy('c.sortOrder', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function buildHierarchicalTree(): array
    {
        // one findAll, children wired up by reference
        $categories = $this->findAll();
        $tree = [];
        $lookup = [];

        foreach ($categories as $category) {
            $lookup[$category->getId()] = [
                'category' => $category,
                'children' => [],
            ];
        }

        foreach ($categories as $category) {
            if ($category->getParent() === null) {
                $tree[$category->getId()] = &$lookup[$category->getId()];
            } else {
                $parentId = $category->getParent()->getId();
                if (isset($lookup[$parentId])) {
                    $lookup[$parentId]['children'][$category->getId()] = &$lookup[$category->getId()];
                }
            }
        }

        return $tree;
    }

    public function findByNameLike(string $searchTerm): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE :term')
            ->setParameter('term', '%' . mb_strtolower($searchTerm) . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getCategoryStats(): array
    {
        $total = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $root = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.parent IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        $leaf = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->leftJoin('c.children', 'children')
            ->where('children.id IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $total,
            'root' => $root,
            'leaf' => $leaf,
            'branch' => $total - $leaf,
        ];
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
