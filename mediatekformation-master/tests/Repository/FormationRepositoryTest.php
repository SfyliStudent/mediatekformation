<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Categorie;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of FormationRepositoryTest
 */
class FormationRepositoryTest extends KernelTestCase {

    protected function setUp(): void {
        parent::setUp();
        self::bootKernel();
        $repository = $this->recupRepository();
        $formations = $repository->findAll();
        foreach ($formations as $formation) {
            $repository->remove($formation, true);
        }

        $playlistRepository = $this->recupPlaylistRepository();
        $playlists = $playlistRepository->findAll();
        foreach ($playlists as $playlist) {
            $playlistRepository->remove($playlist, true);
        }
    }

    
    /**
     * Récupère le repository de Formation
     */
    public function recupRepository(): FormationRepository{
        self::bootKernel();
        return self::getContainer()->get(FormationRepository::class);
    }
    public function recupPlaylistRepository(): PlaylistRepository {
        self::bootKernel();
        $repository = self::getContainer()->get(PlaylistRepository::class);
        return $repository;
    }

    /**
     * Récupère le nombre d'enregistrements contenus dans la table Formation
     */
public function testNbFormations(){
    $repository = $this->recupRepository();
    
    // Ajouter des formations de test
    for ($i = 0; $i < 241; $i++) {
        $formation = new Formation();
        $formation->setTitle("Formation " . $i)
                  ->setDescription("Description " . $i)
                  ->setPublishedAt(new \DateTime());
        $repository->add($formation, true);
    }

    $nbFormations = $repository->count([]);
    $this->assertEquals(241, $nbFormations);
}


    /**
     * Création d'une instance de Formation avec les champs
     * @param string $title
     * @return Formation
     */
    public function newFormation(string $title, DateTime $publishedAt = null): Formation {
    if ($publishedAt === null) {
        $publishedAt = new DateTime("2023-01-16 13:33:39");
    }
    $formation = (new Formation())
            ->setTitle($title)
            ->setDescription("DESCRIPTION DE FORMATIONDETEST")
            ->setPublishedAt($publishedAt);
    return $formation;
}


    public function testAddFormation() {
        $repository = $this->recupRepository();
        
        // Générer un titre unique pour la formation
        $uniqueTitle = "FORMATION TEST " . uniqid();
        $formation = $this->newFormation($uniqueTitle);
        
        $nbFormations = $repository->count([]);
        $repository->add($formation, true);
        $this->assertEquals($nbFormations + 1, $repository->count([]), "erreur lors de l'ajout");
    }

    public function testRemoveFormation(){
        $repository = $this->recupRepository();
        
        // Générer un titre unique pour la formation
        $uniqueTitle = "FORMATION TEST " . uniqid();
        $formation = $this->newFormation($uniqueTitle);
        
        $repository->add($formation, true);
        $nbFormations = $repository->count([]);
        $repository->remove($formation, true);
        $this->assertEquals($nbFormations - 1, $repository->count([]), "erreur lors de la suppression");
    }

    public function testFindAllOrderBy(){
        $repository = $this->recupRepository();

        // Ajout de plusieurs formations pour le test
        $formationsTitles = [
            "Formation A " . uniqid(),
            "Formation B " . uniqid(),
            "Formation C " . uniqid(),
            "Android Studio (complément n°1) : Navigation Drawer et Fragment"
        ];

        foreach ($formationsTitles as $title) {
            $formation = $this->newFormation($title);
            $repository->add($formation, true);
        }

        $formations = $repository->findAllOrderBy("title", "ASC");
        $nbFormations = count($formations);

        // Nous vérifions le nombre de formations ajoutées pour ce test
        $this->assertEquals(count($formationsTitles), $nbFormations);

        // Vérifiez que le titre de la première formation triée est correct
        $this->assertEquals("Android Studio (complément n°1) : Navigation Drawer et Fragment", $formations[0]->getTitle());
    }

    public function testFindAllOrderByTable(){
    $repository = $this->recupRepository();

    // Création de la playlist
    $entityManager = self::getContainer()->get('doctrine')->getManager();
    $playlist = new Playlist();
    $playlist->setName('Test Playlist ' . uniqid());
    $entityManager->persist($playlist);
    $entityManager->flush();

    // Ajout de plusieurs formations pour le test avec des titres uniques
    $formationsTitles = [
        "Formation A " . uniqid(),
        "Formation B " . uniqid(),
        "Formation C " . uniqid()
    ];

    foreach ($formationsTitles as $title) {
        $formation = $this->newFormation($title);
        $formation->setPlaylist($playlist);
        $repository->add($formation, true);
    }

    $formations = $repository->findAllOrderBy("name", "ASC", "playlist");
    $nbFormations = count($formations);

    // Vérifiez le nombre de formations ajoutées pour ce test
    $this->assertEquals(count($formationsTitles), $nbFormations);

    // Vérifiez que le titre de la première formation triée est correct
    $this->assertStringStartsWith("Formation A", $formations[0]->getTitle());
}



    public function testFindByContainValue() {
        $repository = $this->recupRepository();

        // Ajout de plusieurs formations pour le test avec des titres spécifiques
        $formationsTitles = [
            "C# : ListBox en couleur",
            "C# avancé",
            "Introduction à C#"
        ];

        foreach ($formationsTitles as $title) {
            $formation = $this->newFormation($title);
            $repository->add($formation, true);
        }

        $formations = $repository->findByContainValue("title", "C#");
        $nbFormations = count($formations);

        // Nous vérifions le nombre de formations ajoutées pour ce test
        $this->assertEquals(count($formationsTitles), $nbFormations);

        // Vérifiez que le titre de la première formation triée est correct
        $this->assertEquals("C# : ListBox en couleur", $formations[0]->getTitle());
    }

    public function testFindByContainValueTable() {
    $repository = $this->recupRepository();
    $entityManager = self::getContainer()->get('doctrine')->getManager();

    // Création de la playlist
    $uniqueName = 'Compléments Android (programmation mobile) ' . uniqid();
    $playlist = new Playlist();
    $playlist->setName($uniqueName);
    $entityManager->persist($playlist);
    $entityManager->flush();

    // Ajout de plusieurs formations pour le test avec des titres uniques
    $formationsTitles = [
        "Android Studio (complément n°13) : Permissions " . uniqid(),
        "Compléments Android (programmation mobile) avancé " . uniqid()
    ];

    foreach ($formationsTitles as $title) {
        $formation = $this->newFormation($title);
        $formation->setPlaylist($playlist);
        $entityManager->persist($formation);
        $repository->add($formation, true);
    }

    $entityManager->flush();  // Ajout de l'appel à flush ici pour s'assurer que tout est bien enregistré

    $formations = $repository->findByContainValue("name", $uniqueName, "playlist");
    $nbFormations = count($formations);

    // Nous vérifions le nombre de formations ajoutées pour ce test
    $this->assertEquals(count($formationsTitles), $nbFormations);

    // Vérifiez que le titre de la première formation triée est correct
    $this->assertStringStartsWith("Android Studio (complément n°13) : Permissions", $formations[0]->getTitle());
}




    public function testFindAllLasted(){
    $repository = $this->recupRepository();
    
    // Générer un titre unique pour la formation
    $uniqueTitle = "FORMATION TEST " . uniqid();
    $publishedAt = new DateTime("2023-01-16 13:33:39");
    $formation = $this->newFormation($uniqueTitle, $publishedAt);
    
    $repository->add($formation, true);
    $formations = $repository->findAllLasted(1);
    $nbFormations = count($formations);
    
    $this->assertEquals(1, $nbFormations);
    $this->assertEquals($publishedAt, $formations[0]->getPublishedAt());
}


    public function testFindAllForOnePlaylist(){
    $entityManager = self::getContainer()->get('doctrine')->getManager();
    $repository = $this->recupRepository();
    
    // Créer une playlist
    $playlist = new Playlist();
    $playlist->setName("Test Playlist");
    $entityManager->persist($playlist);
    $entityManager->flush(); // S'assurer que la playlist est persistée
    
    // Générer un titre unique pour la formation
    $uniqueTitle = "FORMATION TEST " . uniqid();
    $formation = $this->newFormation($uniqueTitle);
    $formation->setPlaylist($playlist);
    
    // Persister la formation
    $entityManager->persist($formation);
    $entityManager->flush(); // S'assurer que la formation est persistée

    // Récupérer les formations pour la playlist
    $formations = $repository->findAllForOnePlaylist($playlist->getId());
    $nbFormations = count($formations);
    
    // Vérifier les résultats
    $this->assertEquals(1, $nbFormations); // Il devrait y avoir une formation dans la playlist
    $this->assertEquals($uniqueTitle, $formations[0]->getTitle());
}


}
