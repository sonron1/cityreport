<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
  public static function getGroups(): array
  {
    return ['villes', 'geo'];
  }

  public function load(ObjectManager $manager): void
  {
    // Liste des 77 communes du Bénin, regroupées par département
    $communesParDepartement = [
        'alibori' => [
            ['nom' => 'Banikoara', 'lat' => 11.2980, 'lng' => 2.4383],
            ['nom' => 'Gogounou', 'lat' => 10.5778, 'lng' => 2.8419],
            ['nom' => 'Kandi', 'lat' => 11.1343, 'lng' => 2.9406],
            ['nom' => 'Karimama', 'lat' => 12.0658, 'lng' => 3.1833],
            ['nom' => 'Malanville', 'lat' => 11.8665, 'lng' => 3.3800],
            ['nom' => 'Segbana', 'lat' => 10.9304, 'lng' => 3.6946]
        ],
        'atakora' => [ // ✅ Corrigé : 'atacora' -> 'atakora'
            ['nom' => 'Boukoumbé', 'lat' => 10.1744, 'lng' => 1.1075],
            ['nom' => 'Cobly', 'lat' => 10.4672, 'lng' => 0.8879],
            ['nom' => 'Kérou', 'lat' => 10.8256, 'lng' => 2.1062],
            ['nom' => 'Kouandé', 'lat' => 10.3305, 'lng' => 1.6887],
            ['nom' => 'Matéri', 'lat' => 10.7064, 'lng' => 1.0497],
            ['nom' => 'Natitingou', 'lat' => 10.3050, 'lng' => 1.3796],
            ['nom' => 'Pehunco', 'lat' => 10.2294, 'lng' => 1.9969],
            ['nom' => 'Tanguiéta', 'lat' => 10.6214, 'lng' => 1.2659],
            ['nom' => 'Toucountouna', 'lat' => 10.5963, 'lng' => 1.3995]
        ],
        'atlantique' => [
            ['nom' => 'Abomey-Calavi', 'lat' => 6.4487, 'lng' => 2.3540],
            ['nom' => 'Allada', 'lat' => 6.6655, 'lng' => 2.1509],
            ['nom' => 'Kpomassè', 'lat' => 6.3979, 'lng' => 2.0396],
            ['nom' => 'Ouidah', 'lat' => 6.3676, 'lng' => 2.0884],
            ['nom' => 'Sô-Ava', 'lat' => 6.4782, 'lng' => 2.4225],
            ['nom' => 'Toffo', 'lat' => 6.8317, 'lng' => 2.0730],
            ['nom' => 'Tori-Bossito', 'lat' => 6.5015, 'lng' => 2.1453],
            ['nom' => 'Zè', 'lat' => 6.7742, 'lng' => 2.2950]
        ],
        'borgou' => [
            ['nom' => 'Bembèrèkè', 'lat' => 10.2293, 'lng' => 2.6636],
            ['nom' => 'Kalalé', 'lat' => 10.2885, 'lng' => 3.3804],
            ['nom' => 'N\'Dali', 'lat' => 9.8614, 'lng' => 2.7175],
            ['nom' => 'Nikki', 'lat' => 9.9401, 'lng' => 3.2100],
            ['nom' => 'Parakou', 'lat' => 9.3400, 'lng' => 2.6303],
            ['nom' => 'Pèrèrè', 'lat' => 10.8280, 'lng' => 3.0153],
            ['nom' => 'Sinendé', 'lat' => 10.3452, 'lng' => 2.2458],
            ['nom' => 'Tchaourou', 'lat' => 8.8854, 'lng' => 2.5986]
        ],
        'collines' => [
            ['nom' => 'Bantè', 'lat' => 8.4177, 'lng' => 1.8783],
            ['nom' => 'Dassa-Zoumè', 'lat' => 7.7773, 'lng' => 2.1872],
            ['nom' => 'Glazoué', 'lat' => 7.9822, 'lng' => 2.2336],
            ['nom' => 'Ouèssè', 'lat' => 8.4856, 'lng' => 2.4186],
            ['nom' => 'Savalou', 'lat' => 7.9283, 'lng' => 1.9753],
            ['nom' => 'Savè', 'lat' => 8.0360, 'lng' => 2.4867]
        ],
        'couffo' => [
            ['nom' => 'Aplahoué', 'lat' => 6.9334, 'lng' => 1.6722],
            ['nom' => 'Djakotomey', 'lat' => 6.8988, 'lng' => 1.7188],
            ['nom' => 'Dogbo', 'lat' => 6.8025, 'lng' => 1.7861],
            ['nom' => 'Klouékanmè', 'lat' => 7.0563, 'lng' => 1.8500],
            ['nom' => 'Lalo', 'lat' => 6.9150, 'lng' => 1.8864],
            ['nom' => 'Toviklin', 'lat' => 6.9410, 'lng' => 1.7926]
        ],
        'donga' => [
            ['nom' => 'Bassila', 'lat' => 9.0088, 'lng' => 1.6656],
            ['nom' => 'Copargo', 'lat' => 9.8280, 'lng' => 1.5428],
            ['nom' => 'Djougou', 'lat' => 9.7086, 'lng' => 1.6681],
            ['nom' => 'Ouaké', 'lat' => 9.6783, 'lng' => 1.3796]
        ],
        'littoral' => [
            ['nom' => 'Cotonou', 'lat' => 6.3677, 'lng' => 2.4253]
        ],
        'mono' => [
            ['nom' => 'Athiémé', 'lat' => 6.5843, 'lng' => 1.6699],
            ['nom' => 'Bopa', 'lat' => 6.5566, 'lng' => 1.9438],
            ['nom' => 'Comè', 'lat' => 6.4073, 'lng' => 1.8827],
            ['nom' => 'Grand-Popo', 'lat' => 6.2830, 'lng' => 1.8247],
            ['nom' => 'Houéyogbé', 'lat' => 6.4995, 'lng' => 1.8700],
            ['nom' => 'Lokossa', 'lat' => 6.6377, 'lng' => 1.7184]
        ],
        'oueme' => [
            ['nom' => 'Adjarra', 'lat' => 6.5556, 'lng' => 2.6928],
            ['nom' => 'Adjohoun', 'lat' => 6.7114, 'lng' => 2.4775],
            ['nom' => 'Aguégués', 'lat' => 6.4776, 'lng' => 2.5284],
            ['nom' => 'Akpro-Missérété', 'lat' => 6.5565, 'lng' => 2.6214],
            ['nom' => 'Avrankou', 'lat' => 6.5542, 'lng' => 2.6486],
            ['nom' => 'Bonou', 'lat' => 6.9028, 'lng' => 2.4989],
            ['nom' => 'Dangbo', 'lat' => 6.5935, 'lng' => 2.5488],
            ['nom' => 'Porto-Novo', 'lat' => 6.4969, 'lng' => 2.6289],
            ['nom' => 'Sèmè-Kpodji', 'lat' => 6.3775, 'lng' => 2.6250]
        ],
        'plateau' => [
            ['nom' => 'Adja-Ouèrè', 'lat' => 7.0141, 'lng' => 2.6300],
            ['nom' => 'Ifangni', 'lat' => 6.6783, 'lng' => 2.7192],
            ['nom' => 'Kétou', 'lat' => 7.3627, 'lng' => 2.6007],
            ['nom' => 'Pobè', 'lat' => 7.0849, 'lng' => 2.6676],
            ['nom' => 'Sakété', 'lat' => 6.7371, 'lng' => 2.6587]
        ],
        'zou' => [
            ['nom' => 'Abomey', 'lat' => 7.1865, 'lng' => 1.9913],
            ['nom' => 'Agbangnizoun', 'lat' => 7.0503, 'lng' => 1.9722],
            ['nom' => 'Bohicon', 'lat' => 7.1794, 'lng' => 2.0663],
            ['nom' => 'Cové', 'lat' => 7.2223, 'lng' => 2.3377],
            ['nom' => 'Djidja', 'lat' => 7.3418, 'lng' => 1.8319],
            ['nom' => 'Ouinhi', 'lat' => 7.0811, 'lng' => 2.4797],
            ['nom' => 'Zagnanado', 'lat' => 7.2541, 'lng' => 2.3302],
            ['nom' => 'Za-Kpota', 'lat' => 7.2308, 'lng' => 2.1792],
            ['nom' => 'Zogbodomey', 'lat' => 7.0011, 'lng' => 2.1180]
        ]
    ];

    // Récupérer tous les départements depuis la base de données
    $departementRepository = $manager->getRepository(Departement::class);

    // Parcourir les départements et créer les villes correspondantes
    foreach ($communesParDepartement as $departementSlug => $communes) {
      // Récupérer le département par son slug
      $departement = $departementRepository->findOneBy(['slug' => $departementSlug]);

      if (!$departement) {
        echo "⚠️  Département avec slug '$departementSlug' non trouvé\n";
        continue;
      }

      foreach ($communes as $communeData) {
        $ville = new Ville();
        $ville->setNom($communeData['nom']);
        $ville->setLatitudeCentre($communeData['lat']);
        $ville->setLongitudeCentre($communeData['lng']);
        $ville->setDepartement($departement);

        // Générer le slug manuellement
        $ville->setSlug($this->slugify($communeData['nom']));

        $manager->persist($ville);

        // Créer une référence pour utilisation ultérieure
        $this->addReference('ville_' . $this->slugify($communeData['nom']), $ville);
      }
    }

    $manager->flush();

    echo "✅ " . count($communesParDepartement) . " départements traités\n";
    $totalCommunes = array_sum(array_map('count', $communesParDepartement));
    echo "✅ $totalCommunes communes créées\n";
  }

  public function getDependencies(): array
  {
    return [
        DepartementFixtures::class,
    ];
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