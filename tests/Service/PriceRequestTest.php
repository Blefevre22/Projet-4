<?php
/**
 * Created by PhpStorm.
 * User: Benoit
 * Date: 26/09/2018
 * Time: 21:28
 */

namespace App\tests\Service;
use App\Entity\PriceList;
use App\Service\DatesService;
use App\Service\PriceRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
//Séquence de test unitaire
class PriceRequestTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    //Test du calcul de la réduction de tarif
    public function testReducedPrice()
    {
        $priceRequest = new PriceRequest($this->entityManager);
        $tarif = $priceRequest->reducedPrice(true, "16");
        $this->assertEquals(6, $tarif);
    }
    //Test de vérification du jour choisi par le visiteur, renvoi badDay si jour de fermeture du musée
    public function testCheckDate()
    {
        $datesService = new DatesService($this->entityManager);
        $date = $datesService->checkDate('30/10/2018');
        $this->assertEquals('badDay', $date['validation']);
    }
}