<?php
/**
 * Created by PhpStorm.
 * User: blefevre
 * Date: 10/10/2018
 * Time: 21:25
 */

namespace App\Service;


class StripeService
{
    //Paiement de la réservation
public function payment($token, $total)
{
// Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey("sk_test_aHN2gqymDFMwMHsN0iqJpxpH");

    $charge = \Stripe\Charge::create([
        'amount' => $total,
        'currency' => 'eur',
        'description' => 'Example charge',
        'source' => $token,
    ]);
}
}