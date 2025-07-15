<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function findAllSorted(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.category', 'c')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('s.sortOrder', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.category = :category')
            ->setParameter('category', $category)
            ->orderBy('s.sortOrder', 'ASC')
            ->addOrderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByNameLike(string $searchTerm): array
    {
        $all = $this->createQueryBuilder('s')
            ->leftJoin('s.category', 'c')
            ->addSelect('c')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();

        $needle = mb_strtolower($searchTerm);
        $results = [];

        foreach ($all as $subject) {
            $nameMatch = mb_strpos(mb_strtolower($subject->getName()), $needle) !== false;
            $synonymMatch = $subject->getSynonym() && mb_strpos(mb_strtolower($subject->getSynonym()), $needle) !== false;

            if ($nameMatch || $synonymMatch) {
                $results[] = $subject;
            }
        }

        return $results;
    }

    public function getSubjectStats(): array
    {
        $total = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $byCategory = $this->createQueryBuilder('s')
            ->select('c.name as category_name, COUNT(s.id) as subject_count')
            ->leftJoin('s.category', 'c')
            ->groupBy('c.id')
            ->orderBy('subject_count', 'DESC')
            ->getQuery()
            ->getResult();

        return [
            'total' => $total,
            'by_category' => $byCategory,
        ];
    }
}
