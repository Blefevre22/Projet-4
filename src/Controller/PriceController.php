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
     * @Route("/jquery-price", name="jquery-price")
     */
    public function jqueryPrice(Request $request)
    {
        $data = $this->requestPrices($request->query->get('date'));
        return new JsonResponse(array('data' => $data));
    }

}
