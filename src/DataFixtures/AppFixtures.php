<?php

namespace App\DataFixtures;

use App\Entity\Arrondissement;
use App\Entity\Categorie;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Enum\StatutSignalement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
  private $passwordHasher;

  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;
  }

  public function getDependencies(): array
  {
    return [
        DepartementFixtures::class,
        VilleFixtures::class,
        ArrondissementFixtures::class,
    ];
  }

  public static function getGroups(): array
  {
    return ['app'];
  }

  public function load(ObjectManager $manager): void
  {
    echo "🚀 Début du chargement d'AppFixtures...\n";

    // Vérifier que les fixtures dépendantes ont été chargées correctement
    if (!$this->hasReference('ville_cotonou', Ville::class)) {
      throw new \RuntimeException('Les fixtures de villes doivent être chargées avant AppFixtures');
    }

    // Récupérer les villes à partir des références
    $villes = [];
    $villeRefs = ['cotonou', 'porto-novo', 'abomey-calavi', 'parakou', 'lokossa'];
    foreach ($villeRefs as $ref) {
      if ($this->hasReference('ville_' . $ref, Ville::class)) {
        $villes[] = $this->getReference('ville_' . $ref, Ville::class);
      }
    }

    if (empty($villes)) {
      throw new \RuntimeException('Aucune ville n\'a été trouvée dans les références');
    }

    echo "✅ " . count($villes) . " villes récupérées des références\n";

    // Récupération des arrondissements par requête directe
    $arrondissementRepository = $manager->getRepository(Arrondissement::class);
    $arrondissements = $arrondissementRepository->findAll();

    echo "✅ " . count($arrondissements) . " arrondissements trouvés en base\n";

    // Préparation d'un tableau d'arrondissements par ville
    $arrondissementsParVille = [];
    foreach ($arrondissements as $arrondissement) {
      $ville = $arrondissement->getVille();
      if ($ville !== null) {
        $villeId = $ville->getId();
        if ($villeId !== null) {
          if (!isset($arrondissementsParVille[$villeId])) {
            $arrondissementsParVille[$villeId] = [];
          }
          $arrondissementsParVille[$villeId][] = $arrondissement;
        }
      }
    }

    // Création de catégories avec icônes et couleurs
    $categories = [];
    $categoriesData = [
        ['nom' => 'Voirie', 'description' => 'Problèmes liés à la chaussée, trottoirs, etc.', 'icone' => 'fa-road', 'couleur' => '#f39c12'],
        ['nom' => 'Éclairage', 'description' => 'Problèmes liés à l\'éclairage public', 'icone' => 'fa-lightbulb', 'couleur' => '#f1c40f'],
        ['nom' => 'Propreté', 'description' => 'Problèmes de déchets, dépôts sauvages, etc.', 'icone' => 'fa-trash', 'couleur' => '#27ae60'],
        ['nom' => 'Espaces verts', 'description' => 'Problèmes dans les parcs et jardins', 'icone' => 'fa-tree', 'couleur' => '#2ecc71'],
        ['nom' => 'Mobilier urbain', 'description' => 'Problèmes avec les bancs, poubelles, etc.', 'icone' => 'fa-bench', 'couleur' => '#8e44ad'],
        ['nom' => 'Assainissement', 'description' => 'Problèmes d\'eaux usées, caniveaux bouchés', 'icone' => 'fa-water', 'couleur' => '#3498db'],
        ['nom' => 'Inondation', 'description' => 'Zones inondées ou à risque d\'inondation', 'icone' => 'fa-water-rise', 'couleur' => '#2980b9'],
    ];

    foreach ($categoriesData as $categorieData) {
      $categorie = new Categorie();
      $categorie->setNom($categorieData['nom']);
      $categorie->setDescription($categorieData['description']);
      $categorie->setIcone($categorieData['icone']);
      $categorie->setCouleur($categorieData['couleur']);
      $manager->persist($categorie);
      $categories[] = $categorie;
    }

    echo "✅ " . count($categories) . " catégories créées\n";

    // Création d'utilisateurs
    $utilisateurs = [];

    // Admin
    $admin = new Utilisateur();
    $admin->setEmail('admin@cityflow.bj');
    $admin->setNom('Admin');
    $admin->setPrenom('Super');
    $admin->setRoles(['ROLE_ADMIN']);
    $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
    $admin->setVilleResidence($villes[0]); // Cotonou
    $admin->setDateInscription(new \DateTime());
    $admin->setEstValide(true);
    $manager->persist($admin);
    $utilisateurs[] = $admin;

    // Modérateur
    $moderateur = new Utilisateur();
    $moderateur->setEmail('modo@cityflow.bj');
    $moderateur->setNom('Modo');
    $moderateur->setPrenom('Super');
    $moderateur->setRoles(['ROLE_MODERATOR']);
    $moderateur->setPassword($this->passwordHasher->hashPassword($moderateur, 'modo123'));
    $moderateur->setVilleResidence($villes[1 % count($villes)]); // Protection contre l'index manquant
    $moderateur->setDateInscription(new \DateTime());
    $moderateur->setEstValide(true);
    $manager->persist($moderateur);
    $utilisateurs[] = $moderateur;

    // Utilisateurs normaux
    $prenoms = ['Kokou', 'Afiavi', 'Koffi', 'Abla', 'Kodjo'];
    $noms = ['Agossou', 'Ahouansou', 'Dossou', 'Tohoun', 'Adoko'];

    for ($i = 0; $i < 5; $i++) {
      $utilisateur = new Utilisateur();
      $utilisateur->setEmail("user{$i}@cityflow.bj");
      $utilisateur->setNom($noms[$i % count($noms)]);
      $utilisateur->setPrenom($prenoms[$i % count($prenoms)]);
      $utilisateur->setRoles(['ROLE_USER']);
      $utilisateur->setPassword($this->passwordHasher->hashPassword($utilisateur, 'user123'));
      $utilisateur->setVilleResidence($villes[$i % count($villes)]);
      $utilisateur->setDateInscription(new \DateTime());
      $utilisateur->setEstValide(true);
      $manager->persist($utilisateur);
      $utilisateurs[] = $utilisateur;
    }

    echo "✅ " . count($utilisateurs) . " utilisateurs créés\n";

    // Création de signalements
    $statuts = [
        StatutSignalement::NOUVEAU,
        StatutSignalement::EN_COURS,
        StatutSignalement::RESOLU,
        StatutSignalement::ANNULE
    ];

    $titres = [
        'Nid de poule dangereux',
        'Lampadaire en panne',
        'Dépôt sauvage de déchets',
        'Caniveau bouché',
        'Graffiti sur mur public',
        'Arbre tombé sur la voie',
        'Fuite d\'eau sur la chaussée',
        'Panneau de signalisation abîmé',
        'Trottoir endommagé',
        'Passage piéton effacé',
        'Inondation après la pluie',
        'Eau stagnante',
        'Route impraticable',
        'Poteaux électriques dangereux',
        'Déversement d\'eaux usées'
    ];

    $descriptions = [
        'Un nid de poule profond est apparu après les dernières pluies, représentant un danger pour les zémidjans et motards.',
        'Le lampadaire ne fonctionne plus depuis plusieurs jours, rendant la rue dangereusement sombre la nuit.',
        'Des déchets ont été déposés illégalement au coin de la rue, créant une nuisance visuelle et olfactive.',
        'Le caniveau est complètement bouché par des déchets et de la boue, causant des inondations à chaque pluie.',
        'Un large graffiti est apparu sur le mur de l\'école municipale, avec des propos inappropriés.',
        'Un arbre est tombé suite à la tempête d\'hier soir, bloquant partiellement la voie.',
        'Une fuite d\'eau importante sur la chaussée depuis ce matin, créant des flaques et rendant la route glissante.',
        'Le panneau de signalisation a été tordu, probablement suite à un choc, et n\'est plus lisible.',
        'Le trottoir présente une fissure importante qui s\'aggrandit et représente un risque de chute pour les piétons.',
        'Les marquages du passage piéton sont presque entièrement effacés, créant un danger à cette intersection fréquentée.',
        'La zone est complètement inondée après les pluies d\'hier, rendant impossible le passage des véhicules et piétons.',
        'L\'eau stagne depuis plusieurs jours, créant un risque sanitaire et favorisant la prolifération de moustiques.',
        'La route est devenue impraticable à cause des trous et de l\'érosion causée par les dernières pluies.',
        'Des poteaux électriques penchent dangereusement et risquent de tomber sur la voie publique.',
        'Les eaux usées sont déversées directement dans la rue, causant des odeurs nauséabondes et un risque sanitaire.'
    ];

    // Créer 30 signalements
    for ($i = 0; $i < 30; $i++) {
      $signalement = new Signalement();
      $signalement->setTitre($titres[$i % count($titres)]);
      $signalement->setDescription($descriptions[$i % count($descriptions)]);
      $signalement->setPhotoUrl('default.jpg');

      // Choisir une ville
      $ville = $villes[$i % count($villes)];
      $signalement->setVille($ville);

      // Associer un arrondissement de cette ville s'il en existe
      $villeId = $ville->getId();
      if (isset($arrondissementsParVille[$villeId]) && !empty($arrondissementsParVille[$villeId])) {
        $index = $i % count($arrondissementsParVille[$villeId]);
        $arrondissement = $arrondissementsParVille[$villeId][$index];
        $signalement->setArrondissement($arrondissement);
      }

      // Coordonnées aléatoires proches de la ville
      $latOffset = (random_int(-100, 100) / 1000);
      $lngOffset = (random_int(-100, 100) / 1000);
      $signalement->setLatitude($ville->getLatitudeCentre() + $latOffset);
      $signalement->setLongitude($ville->getLongitudeCentre() + $lngOffset);

      $signalement->setDateSignalement(new \DateTime("- {$i} days"));
      $signalement->setStatut($statuts[$i % count($statuts)]);
      $signalement->setPriorite(($i % 3) + 1);
      $signalement->setEtatValidation('validé');
      $signalement->setUtilisateur($utilisateurs[$i % count($utilisateurs)]);
      $signalement->setCategorie($categories[$i % count($categories)]);

      $manager->persist($signalement);
    }

    echo "✅ 30 signalements créés\n";

    $manager->flush();

    echo "🎉 AppFixtures terminé avec succès !\n";
  }
}