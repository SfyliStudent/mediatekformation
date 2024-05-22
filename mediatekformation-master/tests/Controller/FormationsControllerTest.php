<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FormationsControllerTest extends WebTestCase {

    private const FORMATIONSPATH = '/formations';
    
    /**
     * Teste l'accès de la page des formations
     */
    public function testAccesPage() {
        $client = static::createClient();
        $crawler = $client->request('GET', self::FORMATIONSPATH);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testPlaylistsTriAsc(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/name/ASC/playlist');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Bases de la programmation n°74 - POO : collections');
    }

    public function testFormationsTriAsc(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/ASC');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Android Studio (complément n°1) : Navigation Drawer et Fragment');
    }

     public function testTriDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'formations/tri/publishedAt/ASC');
        $this->assertSelectorTextContains('th', 'formation');
        $this->assertCount(5, $crawler->filter('th'));
        $this->assertSelectorTextContains('h5', 'Cours UML (1 à 7 / 33) : introduction');
    }

     public function testFiltreFormations()
    {
        $client = static::createClient();
        $client->request('GET', '/formations'); 
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'UML'
        ]);
        //vérifie le nombre de lignes obtenues
        $this->assertCount(10, $crawler->filter('h5'));
        // vérifie si la formation correspond à la recherche
         $this->assertSelectorTextContains('h5', 'UML');
    }

     public function testFiltrePlaylists()
    {
        $client = static::createClient();
        $client->request('GET', '/formations/recherche/name/playlist'); 
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Eclipse'
        ]);
        //vérifie le nombre de lignes obtenues
        $this->assertCount(9, $crawler->filter('h5'));
        // vérifie si la formation correspond à la recherche
         $this->assertSelectorTextContains('h5', 'Eclipse');
    }

    public function testFiltreCategories()
    {
        $client = static::createClient();
        $client->request('GET', '/formations/recherche/id/categories'); 
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Java'
        ]);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        echo $content;
        //vérifie le nombre de lignes obtenues
        $this->assertCount(7, $crawler->filter('h5'));
        // vérifie si la formation correspond à la recherche
         $this->assertSelectorTextContains('h5', 'Java');
    }

    public function testLinkFormations() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/formations');

    // Vérifiez que la réponse initiale est correcte
    $this->assertResponseIsSuccessful();

    // Débogage : afficher le contenu de la page pour vérifier le lien
    $content = $client->getResponse()->getContent();
    echo "Page après requête GET /formations:\n";
    echo $content;

    // Vérifiez que l'image avec l'attribut alt="image de la formation" existe avant d'essayer de cliquer sur le lien
    $this->assertGreaterThan(0, 
            $crawler->filter('a > img[alt="image de la formation"]')->count(),
            'Le lien "image de la formation" est introuvable.');

    // Sélectionner et cliquer sur le lien
    $link = $crawler->filter('a > img[alt="image de la formation"]')->ancestors('a')->link();
    $client->click($link); // Effectuer le clic sans réassigner $crawler

    // Vérifiez que la réponse après le clic est correcte
    $this->assertResponseIsSuccessful();
    $uri = $client->getRequest()->server->get("REQUEST_URI");
    $this->assertEquals('/formations/formation/1', $uri);
}
public function testShowOneFormation()
{
    $client = static::createClient();
    $crawler = $client->request('GET', '/formations/formation/1');

    // Vérifiez que la réponse est un succès
    $this->assertResponseIsSuccessful();

    // Déboguer le contenu de la réponse
    $responseContent = $client->getResponse()->getContent();
    file_put_contents('/tmp/response.html', $responseContent);

    // Assurez-vous que le contenu de la réponse contient les informations attendues
    $this->assertStringContainsString('Formation Title', $responseContent); // Remplacez 'Formation Title' par le titre attendu de la formation
    $this->assertStringContainsString('Formation Description', $responseContent); // Remplacez 'Formation Description' par la description attendue
}
}