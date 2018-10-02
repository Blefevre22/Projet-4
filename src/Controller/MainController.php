<?php

namespace App\Controller;

use App\Entity\Booking;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookingType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Swift_Mailer;
use App\Service\PriceRequest;

class MainController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        //Création d'un objet Booking
        $booking = new Booking();
        //Appel de l'entity Manager
        $em = $this->getDoctrine()->getManager();
        //Vérification des journées à 1000 réservations
        $checkBookings =  $em->getRepository(Booking::class)->getCheckLimitBooking();
        $disableDate = ["05-01","11-01","12-25"];
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
            'disableDate' => $disableDate,
            'toto' => 'toto'
        ]);
    }

    /**
     * @Route("/summary", name="summary")
     */
    public function summary(PriceRequest $priceRequest, Request $request)
    {
        //Si le parametre request est une méthode POST
        if ($request->isMethod('POST')) {
            $booking = new Booking();
            $form = $this->createForm(BookingType::class, $booking);
            //Récupération des valeurs dans le formulaire
            $form->handleRequest($request);
            //Si le formulaire est valide
            if ($form->isValid()) {
                foreach ($booking->getCustomer() as $customer) {
                    $price = $priceRequest->requestPrices($customer->getBirthDate());
                    if($customer->getReduced() === true && $price > 10){
                        $customer->setPrice($price - 10);
                    }elseif($customer->getReduced() === true && $price < 10){
                        $customer->setPrice(0);
                    }else{
                        $customer->setPrice($price);
                    }
                }
                $this->modelizeBooking($booking);
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
        $session = new Session();
        $em = $this->getDoctrine()->getManager();
        $dateRegistration = str_replace('/','-',$booking->getRegistrationDate());
        $dateRegistration = new \DateTime($dateRegistration);
        $booking->setRegistrationDate($dateRegistration);
        $maxCounter = $em->getRepository(Booking::class)->getCheckCounter($booking->getRegistrationDate());
        $booking->setCounter($maxCounter['counter'] + 1);
        foreach ($booking->getCustomer() as $customer) {
            $customer->setBooking($booking);
            $birthday = str_replace('/','-',$customer->getBirthDate());
            $birthday = new \DateTime($birthday);
            $customer->setBirthDate($birthday);
        }
        $session->set('booking', $booking);
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
        $session = new Session();
        // Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey("sk_test_aHN2gqymDFMwMHsN0iqJpxpH");

// Token is created using Checkout or Elements!
// Get the payment token ID submitted by the form:
        $token = $_POST['stripeToken'];

        $charge = \Stripe\Charge::create([
            'amount' => $request->get('total'),
            'currency' => 'eur',
            'description' => 'Example charge',
            'source' => $token,
        ]);
        try{
            $this->addFlash(
                'notice',
                'Votre réservation est enregistrée !'
            );
            $booking = $session->get('booking');
            $this->sendMail($booking);
            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->flush();
            $session->clear();
            return $this->redirectToRoute('main');
        }catch(\Stripe\Error\Card $e){
            return $this->redirectToRoute('refusedPayment');
        }
        $this->addFlash(
            'notice',
            'Votre réservation est enregistrée !'
        );
        $booking = $session->get('booking');
        $this->sendMail($booking);
        $em = $this->getDoctrine()->getManager();
        $em->persist($booking);
        $em->flush();
        $session->clear();
        return $this->redirectToRoute('main');
    }
    public function sendMail($booking)
    {
        $mailer = $this->container->get('swiftmailer.mailer.default');
        $message = (new \Swift_Message('Louvre : votre réservation'))
            ->setFrom('benoit.lefevre22@gmail.com')
            ->setTo($booking->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    array('booking' => $booking)
                ),
                'text/html'
            );
        $mailer->send($message);
    }
}
