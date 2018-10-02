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
        $data = $priceRequest->requestPrices($request->query->get('date'));
        return new JsonResponse(array('data' => $data));
    }

    /**
     * @Route("/jquery-ticket", name="jquery-ticket")
     */
    public function jqueryTicket(Request $request)
    {
        $data = $request->query->get('date');
        $data = str_replace('/', '-', $data);
        $registration = new \DateTime($data);
        $today = new \DateTime();
        if($today->format('d-m-y') === $registration->format('d-m-y')){
            if($today->format('H') > '14'){
                $response = true;
            }else{
                $response = false;
            }
        }else{
            $response = false;
        }
        return new JsonResponse(array('data' => $response));
    }

}
