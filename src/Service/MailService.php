<?php
/**
 * Created by PhpStorm.
 * User: blefevre
 * Date: 10/10/2018
 * Time: 21:38
 */

namespace App\Service;

class MailService
{
    private $mailer;
    private $templating;
    //Appel des bundle
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer     = $mailer;
        $this->templating = $templating;
    }
    //Envoi d'un mail d'information sur la réservation
    public function sendMail($booking)
    {
        $message = (new \Swift_Message('Louvre : votre réservation'))
            ->setFrom('benoit.lefevre22@gmail.com')
            ->setTo($booking->getEmail())
            ->setBody(
                $this->templating->render(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    array('booking' => $booking)
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}