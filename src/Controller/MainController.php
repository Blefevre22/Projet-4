<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\PriceList;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookingType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swift_Mailer;

class MainController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request)
    {
        //Création d'un objet Booking
        $booking = new Booking();
        //Appel de l'entity Manager
        $em = $this->getDoctrine()->getManager();
        //Vérification des journées à 1000 réservations
        //$checkBookings =  $em->getRepository(Booking::class)->getCheckLimitBooking();
        $disableDate = [];
        //foreach($checkBookings as $booking){
          //  $disableDate[] = $booking['registrationDate']->format('Y-m-d');
        //}
        //dump($disableDate);
        //Création du formulaire basé sur Booking
        $form = $this->createform(BookingType::class, $booking);
        //Si le parametre request est une méthode POST
        if ($request->isMethod('POST')) {
            //Récupération des valeurs dans le formulaire
            $form->handleRequest($request);
            //Si le formulaire est valide
            if ($form->isValid()) {
                //Methode de validation des tickets
                $this->validTicket($booking);
            }
        }
        //Appel de la vue
        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            //'disableDate' => $disableDate
        ]);
    }
    public function sendMail($booking)
    {
        $mailer = $this->container->get('swiftmailer.mailer.default');
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('benoit.lefevre22@gmail.com')
            ->setTo($booking->getEmail())
            ->setBody('Bonjour '.$booking->getcustomer()[0]->getName());
        $mailer->send($message);
    }
    public function validTicket($booking)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($booking->getCustomer() as $customer) {
            $price = $this->requestPrices($customer->getBirthDate());
            $customer->setBooking($booking);
            $customer->setPrice($price);
        }
        $em->persist($booking);
        $em->flush();
    }
    /**
     * @Route("/request-price", name="request-price")
     */
    public function requestPrices($date)
    {
        $em = $this->getDoctrine()->getManager();
        $today = new \DateTime();
        if(is_string($date)) {
           $date = new \DateTime($date);
        }
        $birthday = new \DateTime($date->format('Y-m-d'));
        $tabTimeAge = $today->diff($birthday);
        $tabTimeAge->format('Y');
        $age= $tabTimeAge->y;
        $price =  $em->getRepository(PriceList::class)->getPriceByBirthday($age);
        return $price['price'];
    }

    /**
     * @Route("/jquery-price", name="jquery-price")
     */
    public function jqueryPrice(Request $request)
    {
        $data = $this->requestPrices($request->query->get('date'));
        return new JsonResponse(array('data' => $data));
    }

    /**
     * @Route("/jquery-checkDay", name="jquery-checkDay")
     */
    public function jqueryCheckDay()
    {
        $em = $this->getDoctrine()->getManager();
        $checkBooking =  $em->getRepository(Booking::class)->getCheckLimitBooking();
        return $checkBooking;
    }
}
