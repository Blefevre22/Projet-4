<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Service\PriceRequest;
use App\Service\MailService;
use App\Service\StripeService;

class PriceController extends Controller
{
    /**
     * @Route("/jquery-ticket", name="jquery-ticket")
     */
    //Vérification ticket journée/demi-journée
    public function jqueryTicket(Request $request)
    {
        //Récupère la date
        $data = $request->query->get('date');
        //Formatage de la date pour correspondre à la BDD
        $data = str_replace('/', '-', $data);
        $registration = new \DateTime($data);
        $today = new \DateTime();
        //Si la date du jour et de réservation est identique
        if($today->format('d-m-y') === $registration->format('d-m-y')){
            //Si l'heure du jour est supérieur à 14H
            if($today->format('H') > '14'){
                $response = true;
            }else{
                $response = false;
            }
        }else{
            $response = false;
        }
        //Retourne le résultat à la page
        return new JsonResponse(array('data' => $response));
    }

    /**
     * @Route("/jquery-price", name="jquery-price")
     */
    //Vérification du tarif
    public function jqueryPrice(PriceRequest $priceRequest, Request $request)
    {
        //Récupère les informations de la page
        $reduced = $request->query->get('reduced');
        $date = $request->query->get('date');
        //Execute une requete via service pour récupérer le tarif en fonction des parametres
        $data = $priceRequest->reducedPrice($date, $reduced);
        //Retourne le résultat à la page
        return new JsonResponse(array('data' => $data));
    }

    /**
     * @Route("/stripe-payment", name="stripe-payment")
     */
    //Paiement et ajout à la BDD
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
                'Votre réservation est enregistrée !, vos billets ont été envoyés par email.'
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
