<?php

namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Entity\Formation;
use App\Entity\Categorie;
use App\Repository\PlaylistRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistsRepositoryTest extends KernelTestCase {

    protected function setUp(): void {
        parent::setUp();
        self::bootKernel();
        $playlistRepository = $this->recupRepository();
        $formationRepository = $this->recupFormationRepository();

        // Supprimer toutes les formations pour éviter les contraintes de clé étrangère
        $formations = $formationRepository->findAll();
        foreach ($formations as $formation) {
            $formationRepository->remove($formation, true);
        }

        // Supprimer toutes les playlists
        $playlists = $playlistRepository->findAll();
        foreach ($playlists as $playlist) {
            $playlistRepository->remove($playlist, true);
        }
    }

    /**
     * Récupère le repository de Playlist
     */
    public function recupRepository(): PlaylistRepository {
        self::bootKernel();
        return self::getContainer()->get(PlaylistRepository::class);
    }

    /**
     * Récupère le repository de Formation
     */
    public function recupFormationRepository(): FormationRepository {
        self::bootKernel();
        return self::getContainer()->get(FormationRepository::class);
    }

    public function testNbPlaylists() {
        $repository = $this->recupRepository();
        $nbPlaylists = $repository->count([]);
        $this->assertEquals(0, $nbPlaylists); // Assurez-vous qu'il n'y a pas de playlists initialement
    }

    public function testAddPlaylist() {
        $repository = $this->recupRepository();
        $uniqueName = "PlaylistDeTest " . uniqid(); // Nom unique
        $playlist = $this->newPlaylist($uniqueName);
        $nbPlaylists = $repository->count([]);
        $repository->add($playlist, true);
        $this->assertEquals($nbPlaylists + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testRemovePlaylist() {
        $repository = $this->recupRepository();
        $uniqueName = "PlaylistDeTest " . uniqid(); // Nom unique
        $playlist = $this->newPlaylist($uniqueName);
        $repository->add($playlist, true);
        $nbPlaylists = $repository->count([]);
        $repository->remove($playlist, true);
        $this->assertEquals($nbPlaylists - 1, $repository->count([]), "erreur lors de la suppression");
    }

    public function testFindAllOrderByName() {
        $repository = $this->recupRepository();

        // Ajouter des playlists pour le test
        $playlistsNames = [
            "PlaylistDeTest A",
            "PlaylistDeTest B",
            "PlaylistDeTest C",
            "PlaylistDeTest D",
            "Android - Test playlist"
        ];

        foreach ($playlistsNames as $name) {
            $playlist = $this->newPlaylist($name);
            $repository->add($playlist, true);
        }

        $playlists = $repository->findAllOrderByName("ASC");
        $nbPlaylists = count($playlists);

        // Vérifier le nombre de playlists
        $this->assertEquals(count($playlistsNames), $nbPlaylists);

        // Vérifier que la première playlist est bien "Android - Test playlist"
        $this->assertEquals("Android - Test playlist", $playlists[0]->getName());
    }

    private function newPlaylist($name) {
        $playlist = new Playlist();
        $playlist->setName($name);
        return $playlist;
    }

    public function testFindAllOrderByNbFormations() {
        $repository = $this->recupRepository();
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

        // Ajouter des playlists pour le test
        $playlistsData = [
            ["name" => "Cours Informatique embarquée", "formations" => 3],
            ["name" => "Playlist A", "formations" => 1],
            ["name" => "Playlist B", "formations" => 2]
        ];

        foreach ($playlistsData as $data) {
            $playlist = $this->newPlaylist($data['name']);
            for ($i = 0; $i < $data['formations']; $i++) {
                $formation = new Formation();
                $formation->setTitle("Formation " . uniqid());
                $entityManager->persist($formation); // Persister explicitement chaque formation
                $playlist->addFormation($formation);
            }
            $entityManager->persist($playlist); // Persister explicitement chaque playlist
        }

        $entityManager->flush(); // Assurez-vous que tout est persisté avant de faire des requêtes

        $playlists = $repository->findAllOrderByAmount("ASC");
        $nbPlaylists = count($playlists);

        // Vérifier le nombre de playlists
        $this->assertEquals(count($playlistsData), $nbPlaylists);

        // Vérifier que la première playlist est bien "Playlist A"
        $this->assertEquals("Playlist A", $playlists[0]->getName());
    }

    public function testFindByContainValue(){
        $repository = $this->recupRepository();
        $playlist = $this->newPlaylist("Playlist Sujet");
        $repository->add($playlist, true);
        $playlists = $repository->findByContainValue("name", "Sujet");
        $nbPlaylists = count($playlists);
        $this->assertEquals(1, $nbPlaylists); // Vérifiez en fonction de vos données de test réelles
        $this->assertEquals("Playlist Sujet", $playlists[0]->getName());
    }

    public function testFindByContainValueTable() {
    $repository = $this->recupRepository();
    $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');

    // Créez une catégorie
    $category = new Categorie();
    $category->setName("MCD");
    $entityManager->persist($category);

    // Créez une formation et associez-la à la catégorie
    $formation = new Formation();
    $formation->setTitle("Formation MCD");
    $formation->addCategory($category);
    $entityManager->persist($formation);

    // Créez une playlist et associez la formation à la playlist
    $playlist = $this->newPlaylist("Playlist MCD");
    $playlist->addFormation($formation);
    $entityManager->persist($playlist);

    // Flushez toutes les entités
    $entityManager->flush();

    // Recherchez les playlists par la valeur "MCD" dans la table "categories"
    $playlists = $repository->findByContainValue("name", "MCD", "categories");
    $nbPlaylists = count($playlists);

    // Vérifiez le nombre de playlists trouvées et leur nom
    $this->assertEquals(1, $nbPlaylists);
    $this->assertEquals("Playlist MCD", $playlists[0]->getName());
}


}


