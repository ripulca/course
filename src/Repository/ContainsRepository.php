<?php

namespace App\Repository;

use App\Entity\Contains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contains>
 *
 * @method Contains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contains[]    findAll()
 * @method Contains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contains::class);
    }

    public function add(Contains $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contains $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getMedsListByCustom($custom){
        return $this->createQueryBuilder('c')
           ->join('App\Entity\Medicine', 'm', 'WITH', 'c.medicine = m')
           ->andWhere('c.custom = :val')
           ->setParameter('val', $custom)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
    }

//    /**
//     * @return Contains[] Returns an array of Contains objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;return $this->createQueryBuilder('SELECT C.id, M.name, M.price, C.amount FROM contains C, medicine M
// WHERE C.medicine_id = M.id AND C.custom_id=:val')
// ->setParameter('val', $customId)
// ->getQuery()
// ->getResult()
// ;
//    }

//    public function findOneBySomeField($value): ?Contains
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
