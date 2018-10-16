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
//Récupère le nombre de réservation et et comptabilise les nouvelles réservations
    public function limitBooking($registration, $customers)
    {
        $registration = str_replace('/','-',$registration);
        $registration = new \DateTime($registration);
        $nbCustomers = count($customers);
        $checkBooking = $this->em->getRepository(Booking::class)->getCheckCounter($registration);
        $totalBooking = $nbCustomers + $checkBooking['counter'];
        if($totalBooking > 1000 ){
            $exceededbooking = $totalBooking - 1000;
            $validBooking = ['validation' =>false, 'exceededBooking' => $exceededbooking];
        }else{
            $validBooking = ['validation' =>true];
        }
        return $validBooking;
    }
}