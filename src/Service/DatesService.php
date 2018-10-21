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
    protected $disableDate = ["05-01","11-01","12-25"];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //Récupère les jours de fermeture, fériés et à plus de 1000 réservations
    public function closeDays()
    {
        //Jours de fermeture du musée
        $disableDate = $this->disableDate;
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
            $validBooking = ['validation' =>'maxBooking', 'exceededBooking' => $exceededbooking];
        }else{
            $validBooking = ['validation' =>'valid'];
        }
        return $validBooking;
    }
    //Vérification de la date, si erreur renvoie false
    public function checkDate($registration)
    {
        $registration = str_replace('/','-',$registration);
        $registration = new \DateTime($registration);
        $today = new \DateTime();
        //Récupération des jours de fermeture
        $disableDate = $this->disableDate;
        //Boucle sur les jours de fermeture
        foreach ($disableDate as $date) {
            //Si date de résercation = date de fermeture, renvoi false
            if($registration->format('m-d') === $date){
                return $validBooking = ['validation' => 'badDay'];
            }
        }
        //Si la date de réservation est un mardi, renvoi false
        if($registration->format('D') === "Tue"){
            return $validBooking = ['validation' => 'badDay'];
            //Sinon si la date de réservation est un jour passé
        }elseif($today->format('Y-m-d') > $registration->format('Y-m-d')){
            return $validBooking = ['validation' => 'badDay'];
        }else{
            return $validBooking = ['validation' => 'valid'];
        }
    }
    //Vérification par appel des deux methodes checkDate et limitBooking
    function checkDateAndBooking($registration, $customers)
    {
        $checkDate = $this->checkDate($registration);
        $limitBooking = $this->limitBooking($registration, $customers);
        if($checkDate['validation'] === "badDay"){
            return $checkDate;
        }else{
            return $limitBooking;
        }
    }
}
