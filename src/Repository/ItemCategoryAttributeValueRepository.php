<?php

namespace App\Repository;

use App\Entity\ItemCategoryAttributeValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemCategoryAttributeValue>
 */
class ItemCategoryAttributeValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemCategoryAttributeValue::class);
    }
}
