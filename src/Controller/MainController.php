<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BddPrepare;
use App\Service\DatesService;
use App\Service\MailService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookingType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Service\PriceRequest;

class MainController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index(DatesService $datesService)
    {
        //Création d'un objet Booking
        $booking = new Booking();
        //Appel de l'entity Manager
        $em = $this->getDoctrine()->getManager();
        //Vérification des journées à 1000 réservations
        $checkBookings =  $em->getRepository(Booking::class)->getCheckLimitBooking();
        $disableDate = $datesService->closeDays($checkBookings);
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

    /**
     * @Route("/summary", name="summary")
     */
    public function summary(PriceRequest $priceRequest, BddPrepare $bddPrepare, Request $request)
    {
        //Si le parametre request est une méthode POST
        if ($request->isMethod('POST')) {
            $booking = new Booking();
            $form = $this->createForm(BookingType::class, $booking);
            //Récupération des valeurs dans le formulaire
            $form->handleRequest($request);
            //Si le formulaire est valide
            if ($form->isValid()) {
                //Boucle sur chaque client
                foreach ($booking->getCustomer() as $customer) {
                    //Service de vérification de réduction et de recherche de tarif
                    $price = $priceRequest->reducedPrice($customer->getBirthDate(), $customer->getReduced());
                    $customer->setPrice($price);
                }
                $bddPrepare->modelizeBookingAndSession($booking);
                //Methode de validation des tickets
                //Appel de la vue
                return $this->render('main/summary.html.twig', [
                    'booking' => $booking
                ]);
            }
        }
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
    public function stripePayment(StripeService $stripeService, MailService $mailService, Request $request){
        $session = new Session();
        //Si le total n'est pas à 0
        if(empty($request->get('validFree'))){
            //Service de paiement de la réservation
            $stripeService->payment($_POST['stripeToken'], $request->get('total'));
        }
        try{
            $this->addFlash(
                'notice',
                'Votre réservation est enregistrée !'
            );
            $booking = $session->get('booking');
            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->flush();
            $mailService->sendMail($booking);
            $session->clear();
            return $this->redirectToRoute('main');
        }catch(\Stripe\Error\Card $e){
            $this->addFlash(
                'notice',
                'Paiement refusé, merci de renouveller votre réservation.'
            );
            return $this->redirectToRoute('main');
        }
    }

}
