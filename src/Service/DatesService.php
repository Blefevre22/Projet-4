<?php
/**
 * Created by PhpStorm.
 * User: blefevre
 * Date: 10/10/2018
 * Time: 21:55
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Booking;

class DatesService
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Récupère les jours de fermeture, fériés et à plus de 1000 réservations
    public function closeDays()
    {
        //Jours de fermeture du musée
        $disableDate = ["05-01","11-01","12-25"];
        //Vérification des journées à 1000 réservations
        $checkBookings =  $this->em->getRepository(Booking::class)->getCheckLimitBooking();
        //Boucle sur chaque dates et les ajoutes au tableau
        foreach($checkBookings as $book){
            $disableDate[] = $book['registrationDate']->format('Y-m-d');
        }
        return $disableDate;
    }
}