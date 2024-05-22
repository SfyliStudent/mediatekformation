<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of PlaylistsControllerTest
 *
 * @author safiya
 */
class PlaylistsControllerTest extends WebTestCase {

    public function testAccesPage(){
       $client = static::createClient();
       $client->request('GET', '/playlists');
       $this->assertResponseStatusCodeSame(Response::HTTP_OK);
   }

    public function testTriPlaylists() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/name/ASC');

        // Vérifiez que la réponse est correcte
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Débogage : afficher le contenu de la page pour vérifier les titres
        $content = $client->getResponse()->getContent();
        echo "Contenu de la page:\n";
        echo $content;

        // Vérifiez que le texte dans l'élément th est correct
        $this->assertSelectorTextContains('th', 'playlist');

        // Vérifiez le nombre d'éléments th
        $this->assertCount(4, $crawler->filter('th'));

        // Vérifiez que le texte correct existe dans h5
        $this->assertSelectorTextContains('h5', 'Bases de la programmation (C#)');
    }

    public function testTriNbFormations() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists/tri/nombre/ASC');

        // Vérifiez que la réponse est correcte
        $this->assertResponseIsSuccessful();

        // Débogage : afficher le contenu de la page pour vérifier les titres
        $content = $client->getResponse()->getContent();
        echo "Contenu de la page:\n";
        echo $content;

        // Vérifiez que le texte dans l'élément th est correct
        $this->assertSelectorTextContains('th', 'playlist');

        // Vérifiez le nombre d'éléments th
        $this->assertCount(4, $crawler->filter('th'));

        // Vérifiez que le texte correct existe dans h5
        $this->assertSelectorTextContains('h5', 'Cours Informatique embarquée');
    }

    public function testFiltrePlaylists() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/playlists'); 
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'sujet'
        ]);

        // Afficher le contenu de la page pour vérifier les résultats du filtre
        $content = $client->getResponse()->getContent();
        echo "Contenu de la page après filtrage:\n";
        echo $content;

        // Vérifier le nombre de lignes obtenues
        $this->assertCount(16, $crawler->filter('h5'));

        // Vérifier si la formation correspond à la recherche
        $this->assertSelectorTextContains('h5', 'sujet');
    }

    public function testFiltreCategories()
{
    $client = static::createClient();
    $client->request('GET', '/playlists/recherche/id/categories'); 
    $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Android'
        ]);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        echo $content;
        //vérifie le nombre de lignes obtenues
        $this->assertCount(4, $crawler->filter('h5'));
        // vérifie si la formation correspond à la recherche
         $this->assertSelectorTextContains('h5', 'Android');
    }
   

    public function testLinkPlaylists() {
    $client = static::createClient();
    $crawler = $client->request('GET', '/playlists');
    
    // Vérifiez que la réponse initiale est correcte
    $this->assertResponseIsSuccessful();
    
    // Débogage : afficher le contenu de la page pour vérifier le lien
    $content = $client->getResponse()->getContent();
    echo "Page après requête GET /playlists:\n";
    echo $content;
    
    // Sélectionner le premier lien "Voir détail" et cliquer dessus
    $link = $crawler->selectLink('Voir détail')->link();
    $client->click($link); // Effectuer le clic sans réassigner $crawler
    
    // Vérifiez que la réponse après le clic est correcte
    $this->assertResponseIsSuccessful();
    
    // Vérifiez que l'URI contient la structure attendue
    $uri = $client->getRequest()->server->get('REQUEST_URI');
    $this->assertMatchesRegularExpression('/\/playlists\/playlist\/\d+/', $uri);
}


}