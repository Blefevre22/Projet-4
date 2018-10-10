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
    //Méthode pour récupérer le prix en fonction de l'age
    public function requestPrices($date)
    {
        //Formatage de la date
        $date = str_replace('/','-', $date);
        $date = new \DateTime($date);
        $today = new \DateTime();
        $birthday = new \DateTime($date->format('Y-m-d'));
        //Compare la date du jour et la date de naissance
        $tabTimeAge = $today->diff($birthday);
        //Récupère l'age du client
        $age= $tabTimeAge->y;
        //Recherche le tarif en BDD via l'age récupéré
        $price =  $this->em->getRepository(PriceList::class)->getPriceByBirthday($age);
        //Retourne le prix
        return $price['price'];
    }
    //Méthode pour définir le prix avec réduction
    public function reducedPrice($date, $reduced)
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