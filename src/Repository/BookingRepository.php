<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Booking::class);
    }

//    /**
//     * @return Booking[] Returns an array of Booking objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getCheckLimitBooking()
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->select('b.registrationDate')
            ->where(':now <= b.registrationDate')
            ->andWhere('b.counter >= 1000')
            ->setParameter('now',new \Datetime())
        ;
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }
    public function getCheckCounter($date)
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->select('b.counter')
            ->where('b.registrationDate = :date')
            ->orderBy('b.counter', 'DESC')
            ->setParameter('date', $date)
            ->setMaxResults(1)
        ;
        return $qb
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
