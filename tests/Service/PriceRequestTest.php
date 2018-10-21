<?php
/**
 * Created by PhpStorm.
 * User: Benoit
 * Date: 26/09/2018
 * Time: 21:28
 */

namespace App\tests\Service;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PriceList;
use PHPUnit\Framework\TestCase;

class PriceRequestTest extends TestCase
{
    //Méthode pour définir le prix avec réduction
    public function reducedPrice('10/10/2018', true)
    {
        //Appel de la méthode de récupération de tarif
        $tarif = $this->requestPrices($date);
        //Si réduction coché
        if($reduced === true OR $reduced === 'true'){
            //Si tarif supérieur à 10€
            if($tarif > 10){
                $tarif = $tarif - 10;
            }else{
                $tarif = 0;
            }
        }
        return $tarif;
    }
}