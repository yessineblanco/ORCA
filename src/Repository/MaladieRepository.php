<?php

namespace App\Repository;

use App\Entity\Maladie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Maladie>
 *
 * @method Maladie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Maladie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Maladie[]    findAll()
 * @method Maladie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MaladieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Maladie::class);
    }

    public function save(Maladie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Maladie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

     /**
     * @return Maladie[]
     */
    public function findPlanBySujet($sujet){
        return $this->createQueryBuilder('Maladie')
            ->andWhere(' Maladie.NomMaladie LIKE :sujet or Maladie.TypeMaladie LIKE :sujet ')
            ->setParameter('sujet', '%'.$sujet.'%')
            ->getQuery()
            ->getResult();
    }

    public function order_By_Nom()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.NomMaladie', 'ASC')
            ->getQuery()->getResult();
    }

    
    public function paginationQuery()
{
  
 return  $this->createQueryBuilder('a')
                 ->orderBy('a.id', 'ASC')
                 ->getQuery();
}

//    /**
//     * @return Maladie[] Returns an array of Maladie objects
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

//    public function findOneBySomeField($value): ?Maladie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
