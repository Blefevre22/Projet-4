<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Booking;
use App\Service\BddPrepare;
use App\Service\DatesService;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Service\PriceRequest;

class MainController extends Controller
{
    /**
     * @Route("/", name="main")
     */
    public function index(DatesService $datesService)
    {
        //Création d'un objet Booking
        $booking = new Booking();
        //Récupère les jours de fermeture, fériés et à plus de 1000 réservations
        $disableDate = $datesService->closeDays();
        //Création du formulaire basé sur Booking
        $form = $this->createForm(BookingType::class, $booking, array(
            'action' => $this->generateUrl('summary')
        ));
        //Appel de la vue
        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            'disableDate' => $disableDate
        ]);
    }

    /**
     * @Route("/summary", name="summary")
     */
    public function summary(PriceRequest $priceRequest, BddPrepare $bddPrepare, Request $request)
    {
        //Si le parametre request est une méthode POST
        if ($request->isMethod('POST')) {
            $booking = new Booking();
            $form = $this->createForm(BookingType::class, $booking);
            //Récupération des valeurs dans le formulaire
            $form->handleRequest($request);
            //Si le formulaire est valide
            if ($form->isValid()) {
                //Boucle sur chaque client
                foreach ($booking->getCustomer() as $customer) {
                    //Service de vérification de réduction et de recherche de tarif
                    $price = $priceRequest->reducedPrice($customer->getBirthDate(), $customer->getReduced());
                    $customer->setPrice($price);
                }
                $bddPrepare->modelizeBookingAndSession($booking);
                //Methode de validation des tickets
                //Appel de la vue
                return $this->render('main/summary.html.twig', [
                    'booking' => $booking
                ]);
            }
        }
    }
    /**
     * @Route("/location", name="location")
     */
    public function location()
    {
        return $this->render('main/location.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render("main/contact.html.twig");
    }
}
