<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Playlist;
use App\Entity\Formation;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Description of CategorieRepositoryTest
 *
 * @autor safiya
 */
class CategorieRepositoryTest extends KernelTestCase {

    protected function setUp(): void {
        parent::setUp();
        self::bootKernel();
        $repository = $this->recupRepository();
        $categories = $repository->findAll();
        foreach ($categories as $categorie) {
            $repository->remove($categorie, true);
        }
    }

    /**
     * Récupère le repository de Catégorie
     */
    public function recupRepository(): CategorieRepository {
        return self::getContainer()->get(CategorieRepository::class);
    }

    /**
     * Récupère le nombre d'enregistrements contenus dans la table Catégorie
     */
    public function testNbCategories() {
        $repository = $this->recupRepository();
        $nbCategories = $repository->count([]);
        // Mettez ici le nombre attendu de catégories dans la base de données
        $this->assertEquals(0, $nbCategories);
    }

    /**
     * Création d'une instance de Catégorie avec les champs
     * @param string $name
     * @return Categorie
     */
    public function newCategorie(string $name): Categorie {
        $categorie = (new Categorie())
                ->setName($name);
        return $categorie;
    }

    public function testAddCategorie() {
        $repository = $this->recupRepository();
        $uniqueName = "CATEGORIE TEST " . uniqid();
        $categorie = $this->newCategorie($uniqueName);
        $nbCategories = $repository->count([]);
        $repository->add($categorie, true);
        $this->assertEquals($nbCategories + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testRemoveCategorie() {
        $repository = $this->recupRepository();
        $uniqueName = "CATEGORIE TEST " . uniqid();
        $categorie = $this->newCategorie($uniqueName);
        $repository->add($categorie, true);
        $nbCategories = $repository->count([]);
        $repository->remove($categorie, true);
        $this->assertEquals($nbCategories - 1, $repository->count([]), "erreur lors de la suppression");
    }

public function testFindAllForOnePlaylist() {
    $entityManager = self::getContainer()->get('doctrine')->getManager();
    $repository = $this->recupRepository();
    
    // Création de la catégorie
    $uniqueName = "CATEGORIE TEST " . uniqid();
    $categorie = $this->newCategorie($uniqueName);
    $repository->add($categorie, true);
    
    // Création de la playlist avec un nom unique
    $uniquePlaylistName = 'Test Playlist ' . uniqid();
    $playlist = new Playlist();
    $playlist->setName($uniquePlaylistName);
    $entityManager->persist($playlist);
    
    // Création de la formation et liaison avec la catégorie et la playlist
    $formation = new Formation();
    $formation->setTitle('Test Formation ' . uniqid());
    $formation->setDescription('Description de la formation');
    $formation->setPublishedAt(new \DateTime());
    $formation->setPlaylist($playlist);
    $formation->addCategory($categorie);
    $entityManager->persist($formation);
    
    $entityManager->flush();
    
    // Vérifiez les catégories pour la playlist avec l'ID de la playlist créée
    $categories = $repository->findAllForOnePlaylist($playlist->getId());
    $nbCategories = count($categories);
    $this->assertEquals(1, $nbCategories);  // Nous avons ajouté une seule catégorie
    $this->assertEquals($uniqueName, $categories[0]->getName());
}



    public function testFindAllOrderBy() {
    $repository = $this->recupRepository();
    
    // Création de plusieurs catégories pour les tests
    $uniqueName1 = "CATEGORIE TEST 1 " . uniqid();
    $uniqueName2 = "CATEGORIE TEST 2 " . uniqid();
    $uniqueName3 = "CATEGORIE TEST 3 " . uniqid();

    $categorie1 = $this->newCategorie($uniqueName1);
    $categorie2 = $this->newCategorie($uniqueName2);
    $categorie3 = $this->newCategorie($uniqueName3);

    $repository->add($categorie1, true);
    $repository->add($categorie2, true);
    $repository->add($categorie3, true);

    // Récupérer les catégories triées par nom
    $categories = $repository->findBy([], ['name' => 'ASC']);
    $nbCategories = count($categories);

    // Vérifier le nombre de catégories
    $this->assertEquals(3, $nbCategories);  // Nous avons ajouté trois catégories

    // Vérifier les noms des catégories triées
    $this->assertEquals($uniqueName1, $categories[0]->getName());
    $this->assertEquals($uniqueName2, $categories[1]->getName());
    $this->assertEquals($uniqueName3, $categories[2]->getName());
}

}




