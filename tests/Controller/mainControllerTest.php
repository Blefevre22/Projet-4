<?php
// tests/Controller/PostControllerTest.php
namespace App\tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
//Test de l'affichage de la page location
    public function testLocation()
    {
        $client = static::createClient();
        $client->request('GET', '/location');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testMain()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Nous contacter")')->count()
        );
    }
}