<?php

namespace App\Repository;

use App\Entity\BonAdresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BonAdresse>
 *
 * @method BonAdresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonAdresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonAdresse[]    findAll()
 * @method BonAdresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonAdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BonAdresse::class);
    }

    public function add(BonAdresse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BonAdresse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return BonAdresse[] Returns an array of BonAdresse objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BonAdresse
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @return BonAdresse[] Returns an array of BonAdresse objects
     */
    public function findBySearch(string $text): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.content LIKE  :val')
            ->setParameter('val', "%$text%")
            ->getQuery()
            ->getResult();
    }
}
