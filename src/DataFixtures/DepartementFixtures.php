<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class DepartementFixtures extends Fixture implements FixtureGroupInterface
{
  public function load(ObjectManager $manager): void
  {
    $departements = [
        [
            'nom' => 'Alibori',
            'description' => 'Département du nord du Bénin, chef-lieu: Kandi',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Atakora',
            'description' => 'Département du nord-ouest du Bénin, chef-lieu: Natitingou',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Atlantique',
            'description' => 'Département du sud du Bénin, chef-lieu: Allada',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Borgou',
            'description' => 'Département du nord-est du Bénin, chef-lieu: Parakou',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Collines',
            'description' => 'Département du centre du Bénin, chef-lieu: Savalou',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Couffo',
            'description' => 'Département du sud-ouest du Bénin, chef-lieu: Aplahoué',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Donga',
            'description' => 'Département du nord-ouest du Bénin, chef-lieu: Djougou',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Littoral',
            'description' => 'Département du sud du Bénin, chef-lieu: Cotonou',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Mono',
            'description' => 'Département du sud-ouest du Bénin, chef-lieu: Lokossa',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Ouémé',
            'description' => 'Département du sud-est du Bénin, chef-lieu: Porto-Novo',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Plateau',
            'description' => 'Département du sud-est du Bénin, chef-lieu: Pobé',
            'pays' => 'Bénin'
        ],
        [
            'nom' => 'Zou',
            'description' => 'Département du centre du Bénin, chef-lieu: Abomey',
            'pays' => 'Bénin'
        ],
    ];

    foreach ($departements as $data) {
      $departement = new Departement();
      $departement->setNom($data['nom']);
      $departement->setDescription($data['description']);
      $departement->setPays($data['pays']);

      // Générer le slug manuellement
      $slug = $this->slugify($data['nom']);
      $departement->setSlug($slug);

      $manager->persist($departement);

      // ✅ Créer une référence pour chaque département
      $this->addReference('departement_' . $slug, $departement);
    }

    $manager->flush();

    echo "✅ " . count($departements) . " départements créés\n";
  }

  public static function getGroups(): array
  {
    return ['departements'];
  }

  private function slugify(string $text): string
  {
    // Remplacer les caractères non alphanumériques par un tiret
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Translitérer
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Supprimer les caractères indésirables
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trimmer
    $text = trim($text, '-');
    // Supprimer les tirets dupliqués
    $text = preg_replace('~-+~', '-', $text);
    // Mettre en minuscules
    $text = strtolower($text);

    return $text;
  }
}