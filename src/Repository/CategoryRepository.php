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

    public function findByNameLike(string $searchTerm): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE :term')
            ->setParameter('term', '%' . mb_strtolower($searchTerm) . '%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
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
