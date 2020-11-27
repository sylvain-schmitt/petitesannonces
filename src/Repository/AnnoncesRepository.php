<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

    /**
     * Return all anonces per page
     * @return void
     */
    public function getPaginatedAnnonces($page, $limit){
        $query = $this->createQueryBuilder('a')
            ->where('a.active = 1')
            ->orderBy('a.created_at', 'DESC')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ;
            return $query->getQuery()->getResult();
    }

    /**
     * Return all anonces per page
     * @return void
     */
    public function getAdminPaginatedAnnonces($page, $limit){
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.created_at', 'DESC')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ;
            return $query->getQuery()->getResult();
    }

    /**
     * return number of annonces
     * @return void
     */
    public function getTotalAnnonces(){

        $query = $this->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.active = 1')
            ;
            return $query->getQuery()->getSingleScalarResult();


    }

      /**
     * return number of annonces
     * @return void
     */
    public function getAdminTotalAnnonces(){

        $query = $this->createQueryBuilder('a')
            ->select('count(a)')
            ;
            return $query->getQuery()->getSingleScalarResult();


    }
    // /**
    //  * @return Annonces[] Returns an array of Annonces objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
