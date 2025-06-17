<?php

namespace App\Command;

use App\Entity\Arrondissement;
use App\Entity\Departement;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-benin-data',
    description: 'Importe les départements, villes et arrondissements du Bénin',
)]
class ImportBeninDataCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Importation des données géographiques du Bénin');
        
        // Vérifier et créer les départements
        $io->section('Importation des départements');
        $departementRepo = $this->entityManager->getRepository(Departement::class);
        
        $departementsData = [
            ['nom' => 'Alibori', 'description' => 'Département du nord du Bénin'],
            ['nom' => 'Atacora', 'description' => 'Département du nord-ouest du Bénin'],
            ['nom' => 'Atlantique', 'description' => 'Département du sud du Bénin'],
            ['nom' => 'Borgou', 'description' => 'Département du nord-est du Bénin'],
            ['nom' => 'Collines', 'description' => 'Département du centre du Bénin'],
            ['nom' => 'Couffo', 'description' => 'Département du sud-ouest du Bénin'],
            ['nom' => 'Donga', 'description' => 'Département du nord-ouest du Bénin'],
            ['nom' => 'Littoral', 'description' => 'Département du sud du Bénin, comprenant Cotonou'],
            ['nom' => 'Mono', 'description' => 'Département du sud-ouest du Bénin'],
            ['nom' => 'Ouémé', 'description' => 'Département du sud-est du Bénin'],
            ['nom' => 'Plateau', 'description' => 'Département du sud-est du Bénin'],
            ['nom' => 'Zou', 'description' => 'Département du centre du Bénin'],
        ];
        
        $departementCount = 0;
        $departements = [];
        
        foreach ($departementsData as $data) {
            $departementExistant = $departementRepo->findOneBy(['nom' => $data['nom']]);
            
            if (!$departementExistant) {
                $departement = new Departement();
                $departement->setNom($data['nom']);
                $departement->setDescription($data['description']);
                
                $this->entityManager->persist($departement);
                $departementCount++;
            } else {
                $departement = $departementExistant;
            }
            
            $departements[$data['nom']] = $departement;
        }
        
        // Flush pour sauvegarder les départements avant d'ajouter les villes
        if ($departementCount > 0) {
            $this->entityManager->flush();
            $io->success("$departementCount nouveaux départements ont été créés.");
        } else {
            $io->info("Tous les départements existent déjà.");
        }
        
        // Importation des villes
        $io->section('Importation des villes principales');
        
        // Tableau associatif: nom de département => [villes]
        $villesParDepartement = [
            'Alibori' => [
                ['nom' => 'Malanville', 'latitude' => 11.8607, 'longitude' => 3.3833],
                ['nom' => 'Kandi', 'latitude' => 11.1342, 'longitude' => 2.9381],
                ['nom' => 'Banikoara', 'latitude' => 11.2981, 'longitude' => 2.4386],
            ],
            'Atlantique' => [
                ['nom' => 'Ouidah', 'latitude' => 6.3676, 'longitude' => 2.0851],
                ['nom' => 'Allada', 'latitude' => 6.6657, 'longitude' => 2.1583],
                ['nom' => 'Abomey-Calavi', 'latitude' => 6.4485, 'longitude' => 2.3536],
            ],
            'Littoral' => [
                ['nom' => 'Cotonou', 'latitude' => 6.3677, 'longitude' => 2.3912],
            ],
            'Ouémé' => [
                ['nom' => 'Porto-Novo', 'latitude' => 6.4969, 'longitude' => 2.6283],
                ['nom' => 'Adjohoun', 'latitude' => 6.7167, 'longitude' => 2.4833],
            ],
            'Mono' => [
                ['nom' => 'Lokossa', 'latitude' => 6.6387, 'longitude' => 1.7153],
                ['nom' => 'Grand-Popo', 'latitude' => 6.2830, 'longitude' => 1.8266],
            ],
            'Couffo' => [
                ['nom' => 'Azovè', 'latitude' => 6.9736, 'longitude' => 1.7319],
                ['nom' => 'Dogbo', 'latitude' => 6.8167, 'longitude' => 1.7833],
            ],
            'Zou' => [
                ['nom' => 'Abomey', 'latitude' => 7.1828, 'longitude' => 1.9916],
                ['nom' => 'Bohicon', 'latitude' => 7.1778, 'longitude' => 2.0662],
            ],
            'Collines' => [
                ['nom' => 'Dassa-Zoumè', 'latitude' => 7.7500, 'longitude' => 2.1833],
                ['nom' => 'Savalou', 'latitude' => 7.9283, 'longitude' => 1.9753],
            ],
            'Plateau' => [
                ['nom' => 'Pobè', 'latitude' => 7.0300, 'longitude' => 2.6600],
                ['nom' => 'Kétou', 'latitude' => 7.3614, 'longitude' => 2.6031],
            ],
            'Borgou' => [
                ['nom' => 'Parakou', 'latitude' => 9.3392, 'longitude' => 2.6278],
                ['nom' => 'Nikki', 'latitude' => 9.9337, 'longitude' => 3.2108],
            ],
            'Atacora' => [
                ['nom' => 'Natitingou', 'latitude' => 10.3042, 'longitude' => 1.3793],
                ['nom' => 'Tanguiéta', 'latitude' => 10.6210, 'longitude' => 1.2670],
            ],
            'Donga' => [
                ['nom' => 'Djougou', 'latitude' => 9.7086, 'longitude' => 1.6680],
                ['nom' => 'Bassila', 'latitude' => 9.0084, 'longitude' => 1.6656],
            ],
        ];
        
        // Tableau d'arrondissements par ville
        $arrondissementsParVille = [
            'Cotonou' => [
                'Akpakpa', 'Cadjehoun', 'Fidjrossè', 'Gbégamey', 'Houéyiho', 'Agla', 
                'Jéricho', 'Midombo', 'Tokplégbé', 'Vossa', 'Xwlacodji', 'Zongo'
            ],
            'Porto-Novo' => [
                'Adjarra', 'Aguégués', 'Avrankou', 'Akpro-Missérété', 'Dangbo'
            ],
            'Parakou' => [
                'Titirou', 'Kpébié', 'Ganhi', 'Albarika', 'Zongo'
            ],
            'Abomey-Calavi' => [
                'Godomey', 'Zinvié', 'Kpanroun', 'Togba', 'Hêvié'
            ],
            'Djougou' => [
                'Kolokondé', 'Patargo', 'Sérou', 'Baréi', 'Bariénou'
            ],
            'Bohicon' => [
                'Agongointo', 'Avogbanna', 'Lissèzoun', 'Saclo', 'Sodohomè'
            ],
            'Abomey' => [
                'Agbokpa', 'Djègbé', 'Hounli', 'Vidolé', 'Zounzounmè'
            ],
        ];
        
        // Créer les villes et arrondissements
        $villeRepo = $this->entityManager->getRepository(Ville::class);
        $arrondissementRepo = $this->entityManager->getRepository(Arrondissement::class);
        
        $villeCount = 0;
        $arrondissementCount = 0;
        $villesMisesAJour = 0;
        
        // Mise à jour pour les villes existantes sans département
        $villesExistantesSansDep = $villeRepo->createQueryBuilder('v')
            ->where('v.departement IS NULL')
            ->getQuery()
            ->getResult();
            
        if (count($villesExistantesSansDep) > 0) {
            // Assigner un département par défaut (Littoral)
            $departementDefaut = $departements['Littoral'] ?? $departements[array_key_first($departements)];
            
            foreach ($villesExistantesSansDep as $ville) {
                $ville->setDepartement($departementDefaut);
                $villesMisesAJour++;
            }
            
            $this->entityManager->flush();
            $io->info("$villesMisesAJour villes existantes ont été assignées au département " . $departementDefaut->getNom());
        }
        
        // Créer les nouvelles villes
        foreach ($villesParDepartement as $nomDepartement => $villes) {
            $departement = $departements[$nomDepartement] ?? null;
            
            if (!$departement) {
                $io->warning("Département non trouvé: $nomDepartement");
                continue;
            }
            
            foreach ($villes as $villeData) {
                // Vérifier si la ville existe déjà
                $villeExistante = $villeRepo->findOneBy(['nom' => $villeData['nom']]);
                
                if ($villeExistante) {
                    // Mise à jour des coordonnées et département
                    $villeExistante->setLatitudeCentre($villeData['latitude']);
                    $villeExistante->setLongitudeCentre($villeData['longitude']);
                    $villeExistante->setDepartement($departement);
                    $ville = $villeExistante;
                } else {
                    // Création d'une nouvelle ville
                    $ville = new Ville();
                    $ville->setNom($villeData['nom']);
                    $ville->setLatitudeCentre($villeData['latitude']);
                    $ville->setLongitudeCentre($villeData['longitude']);
                    $ville->setDepartement($departement);
                    
                    $this->entityManager->persist($ville);
                    $villeCount++;
                }
                
                // Ajouter des arrondissements pour certaines villes
                if (isset($arrondissementsParVille[$villeData['nom']])) {
                    foreach ($arrondissementsParVille[$villeData['nom']] as $nomArrondissement) {
                        // Vérifier si l'arrondissement existe déjà
                        $arrondissementExistant = $arrondissementRepo->findOneBy([
                            'nom' => $nomArrondissement, 
                            'ville' => $ville
                        ]);
                        
                        if (!$arrondissementExistant) {
                            $arrondissement = new Arrondissement();
                            $arrondissement->setNom($nomArrondissement);
                            $arrondissement->setDescription("Arrondissement de {$villeData['nom']}");
                            $arrondissement->setVille($ville);
                            
                            $this->entityManager->persist($arrondissement);
                            $arrondissementCount++;
                        }
                    }
                }
            }
        }
        
        // Sauvegarder toutes les entités
        $this->entityManager->flush();
        
        // Afficher un résumé
        $io->success([
            "Importation terminée avec succès !",
            "$departementCount nouveaux départements ont été créés.",
            "$villeCount nouvelles villes ont été créées.",
            "$arrondissementCount nouveaux arrondissements ont été créés."
        ]);

        return Command::SUCCESS;
    }
}