<?php
/**
 * Created by PhpStorm.
 * User: blefevre
 * Date: 10/10/2018
 * Time: 20:31
 */

namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Booking;

class BddPrepare
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    //Prépare l'objet booking pour l'ajout à la BDD
    public function modelizeBookingAndSession($booking)
    {
        $session = new Session();
        $dateRegistration = str_replace('/','-',$booking->getRegistrationDate());
        $dateRegistration = new \DateTime($dateRegistration);
        $booking->setRegistrationDate($dateRegistration);
        $maxCounter = $this->em->getRepository(Booking::class)->getCheckCounter($booking->getRegistrationDate());
        $booking->setCounter($maxCounter['counter'] + 1);
        foreach ($booking->getCustomer() as $customer) {
            $customer->setBooking($booking);
            $birthday = str_replace('/','-',$customer->getBirthDate());
            $birthday = new \DateTime($birthday);
            $customer->setBirthDate($birthday);
        }
        //Retourne une session contenant les informations de réservation
        return $session->set('booking', $booking);
    }
}