<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\PriceList;
use App\Service\PriceRequest;

class PriceController extends Controller
{
    /**
     * @Route("/jquery-price", name="jquery-price")
     */
    public function jqueryPrice(PriceRequest $priceRequest, Request $request)
    {
        //Execute une requete via service pour récupérer le tarif en fonction de la date
        $data = $priceRequest->requestPrices($request->query->get('date'));
        //Retourne le résultat à la page
        return new JsonResponse(array('data' => $data));
    }

    /**
     * @Route("/jquery-ticket", name="jquery-ticket")
     */
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
     * @Route("/jquery-reduced", name="jquery-reduced")
     */
    public function jqueryReduced(PriceRequest $priceRequest, Request $request)
    {
        //Récupère les informations de la page
        $reduced = $request->query->get('reduced');
        $date = $request->query->get('date');
        //Execute une requete via service pour récupérer le tarif en fonction des parametres
        $data = $priceRequest->reducedPrice($date, $reduced);
        //Retourne le résultat à la page
        return new JsonResponse(array('data' => $data));
    }
}
