<?php

namespace App\Repository;

use App\Entity\Medicine;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Medicine>
 *
 * @method Medicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medicine[]    findAll()
 * @method Medicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicine::class);
    }

    public function add(Medicine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Medicine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllMeds($currentPage = 1)
    {
        // Create our query
        $query = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'DESC')
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }

    public function getAllMed()
    {
        // Create our query
        return $this->createQueryBuilder('m')
            ->orderBy('m.id')
            ->getQuery()
            ->getResult();
    }
    
    public function paginate($dql, $page = 1, $limit = 8)
    {
        $paginator = new Paginator($dql);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit

        return $paginator;
    }

    public function searchByQuery(string $query, $currentPage=1){
        $Query=$this->createQueryBuilder('m')
        ->where("m.name LIKE :query or m.pharmgroup LIKE :query")
        ->setParameter("query", "%".$query."%")
        ->getQuery();
        $paginator = $this->paginate($Query, $currentPage);
        return $paginator;
    }

    public function getMedStatPr(){
        return $this->createQueryBuilder('m')
        ->select('SUM(p.amount) AS in_stock')
        ->join('App\Entity\Provides', 'p', 'WITH', 'p.medicine=m')
        ->groupBy('m.id')
        ->orderBy('m.id')
        ->getQuery()
        ->getResult();
    }

    public function getMedStatCn(){
        return $this->createQueryBuilder('m')
        ->select('SUM(c.amount) AS bought')
        ->join('App\Entity\Contains', 'c', 'WITH', 'c.medicine=m')
        ->groupBy('m.id')
        ->orderBy('m.id')
        ->getQuery()
        ->getResult();
    }
//    /**
//     * @return Medicine[] Returns an array of Medicine objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Medicine
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
