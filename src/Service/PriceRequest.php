<?php
/**
 * Created by PhpStorm.
 * User: Benoit
 * Date: 26/09/2018
 * Time: 21:28
 */

namespace App\Service;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PriceList;
class PriceRequest
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function requestPrices($date)
    {
        $date = str_replace('/','-', $date);
        $date = new \DateTime($date);
        $today = new \DateTime();
        $birthday = new \DateTime($date->format('Y-m-d'));
        $tabTimeAge = $today->diff($birthday);
        $age= $tabTimeAge->y;
        $price =  $this->em->getRepository(PriceList::class)->getPriceByBirthday($age);
        return $price['price'];
    }
    public function reducedPrice($date, $reduced)
    {
        $tarif = $this->requestPrices($date);
        if($reduced === 'true'){
            if($tarif > 10){
                $tarif = $tarif - 10;
            }else{
                $tarif = 0;
            }
        }
        return $tarif;
    }
}