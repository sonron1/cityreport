<?php

namespace App\DataFixtures;

use App\Entity\Arrondissement;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArrondissementFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
  public function load(ObjectManager $manager): void
  {
    // Liste des arrondissements par commune
    $arrondissementsParCommune = [
      // LITTORAL
        'cotonou' => [
            'Akpakpa', 'Cadjèhoun', 'Fidjrossè', 'Gbégamey', 'Houéyiho', 'Agla',
            'Jéricho', 'Midombo', 'Tokplégbé', 'Vossa', 'Xwlacodji', 'Zongo', 'Sikècodji'
        ],

      // OUÉMÉ
        'porto-novo' => [
            'Adjarra', 'Houézounmè', 'Attaké', 'Djassin', 'Tokpota', 'Ouando',
            'Dowa', 'Houinmè', 'Akonaboè', 'Djègan-Daho'
        ],
        'adjarra' => ['Adjarra I', 'Adjarra II', 'Honvié', 'Médédjonou', 'Aglogbè'],
        'adjohoun' => ['Adjohoun', 'Akpadanou', 'Awonou', 'Azowlissè', 'Démè', 'Kodé', 'Togbota'],
        'aguegues' => ['Avagbodji', 'Houédomè', 'Zoungamè'],
        'akpro-misserete' => ['Akpro-Missérété', 'Gomè-Sota', 'Katagon', 'Vakon', 'Zoungbomè'],
        'avrankou' => ['Atchoukpa', 'Avrankou', 'Djomon', 'Gbozounmè', 'Kouty', 'Ouanho', 'Sado'],
        'bonou' => ['Affamè', 'Atchonsa', 'Bonou-Centre', 'Damè-Wogon', 'Houinviguè'], // ✅ Renommé "Bonou" en "Bonou-Centre"
        'dangbo' => ['Dangbo', 'Dèkin', 'Gbéko', 'Hozin', 'Késsounou', 'Zounguè'],
        'seme-kpodji' => ['Agblangandan', 'Aholouyèmè', 'Djrègbè', 'Ekpè', 'Sèmè-Kpodji', 'Tohouè'],

      // ATLANTIQUE
        'abomey-calavi' => ['Abomey-Calavi', 'Akassato', 'Godomey', 'Golo-Djigbé', 'Hêvié', 'Togba'],
        'allada' => ['Allada', 'Awe', 'Hinvi', 'Lissègazoun', 'Lon-Agonmè', 'Sékou'],
        'kpomasse' => ['Dékanmè', 'Kpomassè', 'Sègbeya', 'Tokplikou'],
        'ouidah' => ['Ouidah I', 'Ouidah II', 'Ouidah III', 'Ouidah IV', 'Pahou', 'Savi'],
        'so-ava' => ['Ahomey-Lokpo', 'Dédomè', 'Ganvié I', 'Ganvié II', 'Houédo-Aguékon', 'Sô-Ava', 'Vekky'],
        'toffo' => ['Ague', 'Damè', 'Gbedjromèdé', 'Toffo'],
        'tori-bossito' => ['Allohoué', 'Ayingba', 'Bossito', 'Tori-Cada'],
        'ze' => ['Adjan', 'Dawé', 'Dodji-Bata', 'Hèkanmè', 'Zè'],

      // BORGOU
        'parakou' => ['1er Arrondissement', '2ème Arrondissement', '3ème Arrondissement'],
        'bembereke' => ['Bembèrèkè', 'Béroubouay', 'Gamia', 'Goumori', 'Ina'],
        'kalale' => ['Anandana', 'Bétérou', 'Kalalé', 'Péonga'],
        'ndali' => ['Bori', 'Gobè', 'N\'Dali', 'Ouénou', 'Sirarou'],
        'nikki' => ['Kpéré', 'Nikki', 'Ouénou', 'Perma'],
        'perere' => ['Gansosso', 'Pèrèrè', 'Yérmarou'],
        'sinende' => ['Boko', 'Dunkassa', 'Kpataba', 'Sinendé'],
        'tchaourou' => ['Alafiarou', 'Bani', 'Sanson', 'Tchaourou'],

      // ALIBORI
        'kandi' => ['Angaradébou', 'Bensékou', 'Donwari', 'Kandi I', 'Kandi II', 'Kandi III'],
        'banikoara' => ['Banikoara', 'Founougo', 'Gomparou', 'Kokey', 'Kokiborou', 'Ounet'],
        'gogounou' => ['Gogounou', 'Sori', 'Zoungou'],
        'karimama' => ['Birni-Lafia', 'Karimama', 'Kompanti', 'Monsey'],
        'malanville' => ['Garou', 'Guéné', 'Madécali', 'Malanville', 'Tomboutou'],
        'segbana' => ['Libantè', 'Liboussou', 'Lougou', 'Segbana'],

      // ATAKORA
        'natitingou' => ['Kotopounga', 'Kouaba', 'Natitingou I', 'Natitingou II', 'Natitingou III'],
        'boukoumbe' => ['Boukoumbé', 'Dipoli', 'Korontière', 'Manta', 'Natta'],
        'cobly' => ['Cobly', 'Datori', 'Kountori', 'Tapoga'],
        'kerou' => ['Barérou', 'Brignamaro', 'Gnemasson', 'Kérou'],
        'kouande' => ['Birni', 'Fô-Bouré', 'Kouandé', 'Oroukayo'],
        'materi' => ['Dassari', 'Gouandé', 'Matéri', 'Nodi', 'Tantéga'],
        'pehunco' => ['Gnérou', 'Pèhunco', 'Tobré'],
        'tanguieta' => ['Batia', 'Koundata', 'Tanguiéta', 'Tanongou'],
        'toucountouna' => ['Tampégré', 'Tchoumi-Tchoumi', 'Toucountouna'],

      // ZOU
        'abomey' => ['Agbokpa', 'Dètohou', 'Djègbè', 'Hounli', 'Sèhoun'],
        'agbangnizoun' => ['Agbangnizoun', 'Kinta', 'Lama', 'Sèhouè', 'Tanvè'],
        'bohicon' => ['Bohicon I', 'Bohicon II', 'Avogbana', 'Lissèzoun', 'Ouassaho'],
        'cove' => ['Cové', 'Houenga', 'Lainta', 'Zannou'],
        'djidja' => ['Agondji', 'Dan', 'Djidja', 'Kinamè', 'Monsourou', 'Set'],
        'ouinhi' => ['Bonou-Ouinhi', 'Dasso', 'Gobé', 'Ouinhi', 'Sagon', 'Tohoué'], // ✅ Déjà renommé dans version précédente
        'zagnanado' => ['Agonlin-Lowé', 'Kpedekpo', 'Kpozoun', 'Zagnanado'],
        'za-kpota' => ['Domè', 'Kpotamè', 'Za-Hla', 'Za-Kpota', 'Zoukou'],
        'zogbodomey' => ['Avlamè', 'Cana I', 'Cana II', 'Zogbodomey'],

      // COLLINES
        'dassa-zoume' => ['Dassa I', 'Dassa II', 'Kèrè', 'Paouignan', 'Soclogbo'],
        'bante' => ['Bantè', 'Bobè', 'Gbanlin', 'Gouka', 'Pira'],
        'glazoue' => ['Assanté', 'Glazoué', 'Gomè', 'Magoumi', 'Sokponta', 'Thio'],
        'ouesse' => ['Kilibo', 'Massa', 'Ouèssè', 'Tchaada'],
        'savalou' => ['Djalloukou', 'Gobada', 'Savalou', 'Tchetti'],
        'save' => ['Bèssè', 'Djabata', 'Guèssou', 'Logozohe', 'Oke-Owo', 'Ottola', 'Savè'],

      // MONO
        'lokossa' => ['Agamè', 'Houin', 'Koudo', 'Lokossa', 'Ouèdèmè'],
        'athieme' => ['Adohoun', 'Athiémé', 'Kpinnou'],
        'bopa' => ['Bopa', 'Lobogo', 'Possotomè', 'Yègodoé'],
        'come' => ['Akodéha', 'Comè', 'Oumako', 'Oumékou'],
        'grand-popo' => ['Agoué', 'Grand-Popo', 'Sèmè'],
        'houeyogbe' => ['Dévé', 'Doutou', 'Houéyogbé'],

      // COUFFO
        'aplahoue' => ['Aplahoué', 'Atomé', 'Azové', 'Dekpo', 'Godohou'],
        'djakotomey' => ['Aga', 'Djakotomey', 'Adjatokoun', 'Houédogli'],
        'dogbo' => ['Ayi', 'Dévé', 'Dogbo-Ahomey', 'Madjrè', 'Totchangni'],
        'klouekanme' => ['Klouékanmè', 'Lanta', 'Tchito'],
        'lalo' => ['Ahomadégbé', 'Gnizounmè', 'Lalo', 'Lokogba', 'Tcharon'],
        'toviklin' => ['Houégbo', 'Itchèdè', 'Kové', 'Toviklin'],

      // DONGA
        'djougou' => ['Djougou I', 'Djougou II', 'Djougou III', 'Kolokondé', 'Bariénou'],
        'bassila' => ['Bassila', 'Manigri', 'Pénéssoulou'],
        'copargo' => ['Birni', 'Copargo', 'Kpébouco', 'Naboua'],
        'ouake' => ['Chabi-Couma', 'Igbéré', 'Ouaké', 'Tchalinga'],

      // PLATEAU
        'pobe' => ['Issaba', 'Pobè', 'Towé'],
        'adja-ouere' => ['Adja-Ouèrè', 'Ikpinlè', 'Totomè'],
        'ifangni' => ['Co-Djodji', 'Daagbé', 'Ifangni', 'Lagbé'],
        'ketou' => ['Idigny', 'Kétou', 'Okpomèta'],
        'sakete' => ['Itakaou', 'Sakété', 'Takon']
    ];

    // Récupérer le repository des villes
    $villeRepository = $manager->getRepository(Ville::class);
    $totalArrondissements = 0;
    $villesTraitees = 0;
    $slugsGeneres = []; // ✅ Tracker pour éviter les doublons

    foreach ($arrondissementsParCommune as $communeSlug => $arrondissements) {
      // Récupérer la ville par son slug
      $ville = $villeRepository->findOneBy(['slug' => $communeSlug]);

      if (!$ville) {
        echo "⚠️  Ville avec slug '$communeSlug' non trouvée\n";
        continue;
      }

      $villesTraitees++;

      foreach ($arrondissements as $nomArrondissement) {
        $arrondissement = new Arrondissement();
        $arrondissement->setNom($nomArrondissement);
        $arrondissement->setDescription("Arrondissement de " . $nomArrondissement . " dans la commune de " . $ville->getNom());
        $arrondissement->setVille($ville);

        // ✅ Générer un slug unique en incluant le nom de la ville
        $slugCandidat = $this->slugify($ville->getSlug() . '-' . $nomArrondissement);

        // ✅ Vérifier l'unicité et ajuster si nécessaire
        $slugFinal = $slugCandidat;
        $compteur = 1;
        while (in_array($slugFinal, $slugsGeneres)) {
          $slugFinal = $slugCandidat . '-' . $compteur;
          $compteur++;
        }

        $slugsGeneres[] = $slugFinal;
        $arrondissement->setSlug($slugFinal);

        $manager->persist($arrondissement);

        // Ajouter une référence pour utilisation future
        $this->addReference('arrondissement_' . $slugFinal, $arrondissement);

        $totalArrondissements++;
      }
    }

    $manager->flush();

    echo "✅ $villesTraitees villes traitées\n";
    echo "✅ $totalArrondissements arrondissements créés\n";
    echo "✅ " . count($slugsGeneres) . " slugs uniques générés\n";
  }

  public static function getGroups(): array
  {
    return ['arrondissements', 'geo'];
  }

  public function getDependencies(): array
  {
    return [
        VilleFixtures::class,
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