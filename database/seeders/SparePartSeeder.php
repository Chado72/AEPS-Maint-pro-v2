<?php

namespace Database\Seeders;

use App\Models\SparePart;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pieces = [
            // Pompes
            [
                'nom' => 'Pompe immergée REDA 4"',
                'reference' => 'PUMP-REDA-4',
                'categorie' => 'Pompe',
                'prix_unitaire' => 150000,
                'stock_min' => 2,
                'stock_actuel' => 5,
                'unite' => 'unité',
                'description' => 'Pompe immergée diamètre 4 pouces, débit 3-5 m³/h'
            ],
            [
                'nom' => 'Pompe immergée PEDROLLO 6"',
                'reference' => 'PUMP-PED-6',
                'categorie' => 'Pompe',
                'prix_unitaire' => 220000,
                'stock_min' => 2,
                'stock_actuel' => 3,
                'unite' => 'unité',
                'description' => 'Pompe immergée diamètre 6 pouces, débit 5-10 m³/h'
            ],
            // Moteurs
            [
                'nom' => 'Moteur triphasé 3kW',
                'reference' => 'MOT-3KW-3PH',
                'categorie' => 'Moteur',
                'prix_unitaire' => 180000,
                'stock_min' => 2,
                'stock_actuel' => 4,
                'unite' => 'unité',
                'description' => 'Moteur électrique triphasé 3kW, 2900 tr/min'
            ],
            [
                'nom' => 'Moteur triphasé 5.5kW',
                'reference' => 'MOT-5.5KW-3PH',
                'categorie' => 'Moteur',
                'prix_unitaire' => 250000,
                'stock_min' => 2,
                'stock_actuel' => 3,
                'unite' => 'unité',
                'description' => 'Moteur électrique triphasé 5.5kW, 2900 tr/min'
            ],
            // Panneaux solaires
            [
                'nom' => 'Panneau solaire 300Wc',
                'reference' => 'SOL-300W',
                'categorie' => 'Solaire',
                'prix_unitaire' => 120000,
                'stock_min' => 4,
                'stock_actuel' => 10,
                'unite' => 'unité',
                'description' => 'Panneau photovoltaïque monocristallin 300Wc'
            ],
            [
                'nom' => 'Panneau solaire 400Wc',
                'reference' => 'SOL-400W',
                'categorie' => 'Solaire',
                'prix_unitaire' => 160000,
                'stock_min' => 4,
                'stock_actuel' => 8,
                'unite' => 'unité',
                'description' => 'Panneau photovoltaïque monocristallin 400Wc'
            ],
            // Onduleurs/Contrôleurs
            [
                'nom' => 'Onduleur pompe solaire 2.2kW',
                'reference' => 'INV-SOL-2.2',
                'categorie' => 'Électronique',
                'prix_unitaire' => 350000,
                'stock_min' => 2,
                'stock_actuel' => 3,
                'unite' => 'unité',
                'description' => 'Onduleur variateur pour pompe solaire 2.2kW max'
            ],
            [
                'nom' => 'Régulateur de charge 60A',
                'reference' => 'REG-60A',
                'categorie' => 'Électronique',
                'prix_unitaire' => 85000,
                'stock_min' => 3,
                'stock_actuel' => 6,
                'unite' => 'unité',
                'description' => 'Régulateur MPPT 60A pour système solaire'
            ],
            // Pièces mécaniques
            [
                'nom' => 'Clapet anti-retour 2"',
                'reference' => 'CLAP-AR-2',
                'categorie' => 'Robinetterie',
                'prix_unitaire' => 25000,
                'stock_min' => 10,
                'stock_actuel' => 20,
                'unite' => 'unité',
                'description' => 'Clapet anti-retour laiton 2 pouces'
            ],
            [
                'nom' => 'Clapet anti-retour 3"',
                'reference' => 'CLAP-AR-3',
                'categorie' => 'Robinetterie',
                'prix_unitaire' => 35000,
                'stock_min' => 10,
                'stock_actuel' => 15,
                'unite' => 'unité',
                'description' => 'Clapet anti-retour laiton 3 pouces'
            ],
            [
                'nom' => 'Câble électrique 4mm²',
                'reference' => 'CAB-4MM',
                'categorie' => 'Électricité',
                'prix_unitaire' => 1500,
                'stock_min' => 100,
                'stock_actuel' => 500,
                'unite' => 'mètre',
                'description' => 'Câble électrique cuivre 4mm², usage pompe'
            ],
            [
                'nom' => 'Câble électrique 6mm²',
                'reference' => 'CAB-6MM',
                'categorie' => 'Électricité',
                'prix_unitaire' => 2200,
                'stock_min' => 100,
                'stock_actuel' => 300,
                'unite' => 'mètre',
                'description' => 'Câble électrique cuivre 6mm², usage pompe'
            ],
            [
                'nom' => 'Pressostat automatique',
                'reference' => 'PRESS-AUTO',
                'categorie' => 'Électronique',
                'prix_unitaire' => 45000,
                'stock_min' => 5,
                'stock_actuel' => 10,
                'unite' => 'unité',
                'description' => 'Pressostat automatique marche/arrêt pompe'
            ],
            [
                'nom' => 'Manomètre 0-10 bars',
                'reference' => 'MANO-10B',
                'categorie' => 'Instrumentation',
                'prix_unitaire' => 15000,
                'stock_min' => 5,
                'stock_actuel' => 12,
                'unite' => 'unité',
                'description' => 'Manomètre glycerine 0-10 bars, diamètre 63mm'
            ],
            [
                'nom' => 'Jeu de joints pompe',
                'reference' => 'JOINT-PUMP',
                'categorie' => 'Consommable',
                'prix_unitaire' => 8000,
                'stock_min' => 20,
                'stock_actuel' => 50,
                'unite' => 'jeu',
                'description' => 'Jeu de joints d\'étanchéité pour pompe immergée'
            ],
            [
                'nom' => 'Condensateur 30µF',
                'reference' => 'COND-30UF',
                'categorie' => 'Électronique',
                'prix_unitaire' => 12000,
                'stock_min' => 10,
                'stock_actuel' => 25,
                'unite' => 'unité',
                'description' => 'Condensateur permanent 30µF 450V'
            ],
            [
                'nom' => 'Condensateur 50µF',
                'reference' => 'COND-50UF',
                'categorie' => 'Électronique',
                'prix_unitaire' => 15000,
                'stock_min' => 10,
                'stock_actuel' => 20,
                'unite' => 'unité',
                'description' => 'Condensateur permanent 50µF 450V'
            ],
            [
                'nom' => 'Tube PVC 2" (barre 4m)',
                'reference' => 'PVC-2IN-4M',
                'categorie' => 'Tuyauterie',
                'prix_unitaire' => 18000,
                'stock_min' => 20,
                'stock_actuel' => 50,
                'unite' => 'barre',
                'description' => 'Tube PVC pression 2 pouces, longueur 4 mètres'
            ],
            [
                'nom' => 'Tube PVC 3" (barre 4m)',
                'reference' => 'PVC-3IN-4M',
                'categorie' => 'Tuyauterie',
                'prix_unitaire' => 28000,
                'stock_min' => 20,
                'stock_actuel' => 40,
                'unite' => 'barre',
                'description' => 'Tube PVC pression 3 pouces, longueur 4 mètres'
            ]
        ];

        foreach ($pieces as $pieceData) {
            SparePart::firstOrCreate(
                ['reference' => $pieceData['reference']],
                $pieceData
            );
        }

        $this->command->info(count($pieces) . ' pièces de rechange créées avec succès !');
    }
}
