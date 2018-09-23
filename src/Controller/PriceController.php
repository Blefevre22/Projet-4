<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\PriceList;

class PriceController extends Controller
{
    /**
     * @Route("/request-price", name="request-price")
     */
    public function requestPrices($date)
    {
        $date = str_replace('/','-', $date);
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


}
