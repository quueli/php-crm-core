<?php

namespace App\Repository;

use App\Entity\Audience;
use App\Entity\Context;
use App\Entity\Item;
use App\Entity\Line;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('i')
            ->select('i', 'l', 'a', 'c')
            ->join('i.line', 'l')
            ->join('i.audience', 'a')
            ->join('i.context', 'c')
            ->orderBy('l.name', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByLine(Line $line): array
    {
        return $this->createQueryBuilder('i')
            ->select('i', 'l', 'a', 'c')
            ->join('i.line', 'l')
            ->join('i.audience', 'a')
            ->join('i.context', 'c')
            ->where('i.line = :line')
            ->setParameter('line', $line)
            ->orderBy('a.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function existsCombination(Line $line, Audience $audience, Context $context, ?Item $exclude = null): bool
    {
        $qb = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.line = :line')
            ->andWhere('i.audience = :audience')
            ->andWhere('i.context = :context')
            ->setParameter('line', $line)
            ->setParameter('audience', $audience)
            ->setParameter('context', $context);

        if ($exclude) {
            $qb->andWhere('i.id != :exclude')
                ->setParameter('exclude', $exclude->getId());
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function getItemStats(): array
    {
        $total = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return ['total' => $total];
    }

    public function search(?Line $line = null, ?Audience $audience = null, ?Context $context = null): array
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i', 'l', 'a', 'c')
            ->join('i.line', 'l')
            ->join('i.audience', 'a')
            ->join('i.context', 'c');

        if ($line) {
            $qb->andWhere('i.line = :line')->setParameter('line', $line);
        }

        if ($audience) {
            $qb->andWhere('i.audience = :audience')->setParameter('audience', $audience);
        }

        if ($context) {
            $qb->andWhere('i.context = :context')->setParameter('context', $context);
        }

        return $qb->orderBy('l.name', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // mb_* in php for the same reason as CategoryRepository::findByNameLike
    public function searchByName(string $query, int $limit = 10): array
    {
        $all = $this->createQueryBuilder('i')
            ->select('i', 'l', 'a', 'c')
            ->join('i.line', 'l')
            ->join('i.audience', 'a')
            ->join('i.context', 'c')
            ->orderBy('l.name', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();

        $needle = mb_strtolower($query);
        $results = [];

        foreach ($all as $item) {
            if (count($results) >= $limit) {
                break;
            }

            $lineMatch = mb_strpos(mb_strtolower($item->getLine()->getName()), $needle) !== false;
            $audienceMatch = mb_strpos(mb_strtolower($item->getAudience()->getName()), $needle) !== false;
            $contextMatch = mb_strpos(mb_strtolower($item->getContext()->getName()), $needle) !== false;

            if ($lineMatch || $audienceMatch || $contextMatch) {
                $results[] = $item;
            }
        }

        return $results;
    }
}
