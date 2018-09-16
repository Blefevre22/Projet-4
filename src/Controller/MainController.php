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
     * @Route("/", name="main ")
     */
    public function index(Request $request)
    {
        //Création d'un objet Booking
        $booking = new Booking();
        //Appel de l'entity Manager
        $em = $this->getDoctrine()->getManager();
        //Vérification des journées à 1000 réservations
        $checkBookings =  $em->getRepository(Booking::class)->getCheckLimitBooking();
        $disableDate = [];
        //Boucle sur chaque dates et les ajoutes au tableau
        foreach($checkBookings as $book){
            $disableDate[] = $book['registrationDate']->format('Y-m-d');
        }
        //Création du formulaire basé sur Booking
        $form = $this->createForm(BookingType::class, $booking);
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
            'disableDate' => $disableDate
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
        $dateRegistration = new \DateTime($booking->getRegistrationDate());
        dump($dateRegistration);
        $booking->setRegistrationDate($dateRegistration);
        foreach ($booking->getCustomer() as $customer) {
            $maxCounter = $em->getRepository(Booking::class)->getCheckCounter($booking->getRegistrationDate());
            $price = $this->requestPrices($customer->getBirthDate());
            $booking->setCounter($maxCounter + 1);
            $customer->setBooking($booking);
            $customer->setPrice($price);
            $birthday = new \DateTime($customer->getBirthDate());
            $customer->setBirthDate($birthday);
        }
        $em->persist($booking);
        $em->flush();
    }
    /**
     * @Route("/request-price", name="request-price")
     */
    public function requestPrices($date)
    {
        dump($date);
        $date = new \DateTime($date);
        $em = $this->getDoctrine()->getManager();
        $today = new \DateTime();
        $birthday = new \DateTime($date->format('Y-m-d'));
        $tabTimeAge = $today->diff($birthday);
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
    /**
     * @Route("/stripe-payment", name="stripe-payment")
     */
    public function stripePayment()
    {
        return $this->render('main/about.html.twig', [

        ]);
    }
}
