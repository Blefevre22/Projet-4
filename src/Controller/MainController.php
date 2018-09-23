<?php

namespace App\Controller;

use App\Entity\Booking;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookingType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swift_Mailer;
use App\Entity\PriceList;

class MainController extends Controller
{
    /**
     * @Route("/", name="main ")
     */
    public function index()
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
        $form = $this->createForm(BookingType::class, $booking, array(
            'action' => $this->generateUrl('summary')
        ));
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
    /**
     * @Route("/summary", name="summary")
     */
    public function summary(Request $request)
    {
        //Si le parametre request est une méthode POST
        if ($request->isMethod('POST')) {
            $booking = new Booking();
            $form = $this->createForm(BookingType::class, $booking);
            //Récupération des valeurs dans le formulaire
            $form->handleRequest($request);
            //Si le formulaire est valide
            if ($form->isValid()) {
                //Methode de validation des tickets
                //Appel de la vue
                return $this->render('main/summary.html.twig', [
                    'booking' => $booking
                ]);
            }
        }

    }
    public function modelizeBooking($booking)
    {
        $em = $this->getDoctrine()->getManager();
        $dateRegistration = str_replace('/','-',$booking->getRegistrationDate());
        $dateRegistration = new \DateTime($dateRegistration);
        $booking->setRegistrationDate($dateRegistration);
        foreach ($booking->getCustomer() as $customer) {
            $maxCounter = $em->getRepository(Booking::class)->getCheckCounter($booking->getRegistrationDate());
            $price = $this->requestPrices($customer->getBirthDate());
            $booking->setCounter($maxCounter['counter'] + 1);
            $customer->setBooking($booking);
            $customer->setPrice($price);
            $birthday = str_replace('/','-',$customer->getBirthDate());
            $birthday = new \DateTime($birthday);
            $customer->setBirthDate($birthday);
        }
    }
    public function validBooking($booking)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($booking);
        $em->flush();
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
    public function stripePayment(Request $request){
        // Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey("sk_test_aHN2gqymDFMwMHsN0iqJpxpH");

// Token is created using Checkout or Elements!
// Get the payment token ID submitted by the form:
        $token = $_POST['stripeToken'];

        $charge = \Stripe\Charge::create([
            'amount' => 999,
            'currency' => 'eur',
            'description' => 'Example charge',
            'source' => $token,
        ]);
    }
}
