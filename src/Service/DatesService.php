<?php
/**
 * Created by PhpStorm.
 * User: blefevre
 * Date: 10/10/2018
 * Time: 21:55
 */

namespace App\Service;


class DatesService
{
public function closeDays($checkBookings)
{
    $disableDate = ["05-01","11-01","12-25"];
    //Boucle sur chaque dates et les ajoutes au tableau
    foreach($checkBookings as $book){
        $disableDate[] = $book['registrationDate']->format('Y-m-d');
    }
    return $disableDate;
}
}