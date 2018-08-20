<?php

namespace App\Repository;

use App\Entity\PriceList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PriceList|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceList|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceList[]    findAll()
 * @method PriceList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PriceList::class);
    }

//    /**
//     * @return PriceList[] Returns an array of PriceList objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PriceList
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getNameForm()
    {
        return $this->createQueryBuilder('p')
            ->select('p.name')
            ->getQuery()->execute();
    }
    public function getPriceByBirthday($age)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->select('p.price')
            ->where(':age >= p.ageMin')
            ->andWhere(':age <= p.ageMax')
            ->setParameter('age', $age)
        ;
        return $qb
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}