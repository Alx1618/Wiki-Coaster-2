<?php

namespace App\Repository;

use App\Entity\Coaster;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coaster>
 */
class CoasterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coaster::class);
    }

    
       
        
        public function findByExampleField($value): array
        {
            return $this->createQueryBuilder('c')
               ->andWhere('c.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('c.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }

      public function findOneBySomeField($value): ?Coaster
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

        public function findFiltered(
            int $parkId = 0, 
            int $categoryId = 0,
            string $search = '',
            int $begin =0,
            int $count =20
            ): Paginator
        {
            $qb = $this->createQueryBuilder('c')
                ->addSelect('p, cat')
                ->leftJoin('c.Park', 'p')
                ->leftJoin('c.Categories', 'cat')
                ->setMaxResults($count)
                ->setFirstResult($begin)
            ;
        
            if ($parkId !== 0) {
                $qb->Where('p.id = :parkId')
                    ->setParameter('parkId', $parkId)
                ;
            }

            if ($categoryId !== 0) {
                $qb->andWhere('cat.id = :categoryId')
                    ->setParameter('categoryId', $categoryId)
                ;
            }

            if(strlen($search) > 2) {
                $qb->andWhere($qb->expr()->like('c.name', 'search'))
                    ->setParameter('search', "%$search%")
                ;
            }
            // Filtrer la catégorie
        
            return new Paginator($qb->getQuery());
        }
        
    }
