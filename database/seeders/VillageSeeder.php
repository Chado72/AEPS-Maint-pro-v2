<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Villages par commune (exemples représentatifs de la province du Yadéga)
        $villagesParCommune = [
            'Ouahigouya' => [
                ['nom' => 'Centre-ville', 'code' => 'OUA-001'],
                ['nom' => 'Gounghin', 'code' => 'OUA-002'],
                ['nom' => 'Tampelce', 'code' => 'OUA-003'],
                ['nom' => 'Koudougou', 'code' => 'OUA-004'],
                ['nom' => 'Sagha', 'code' => 'OUA-005']
            ],
            'Bourzanga' => [
                ['nom' => 'Bourzanga Centre', 'code' => 'BOU-001'],
                ['nom' => 'Kamsé', 'code' => 'BOU-002'],
                ['nom' => 'Mongomdé', 'code' => 'BOU-003'],
                ['nom' => 'Pissiga', 'code' => 'BOU-004']
            ],
            'Fassapel' => [
                ['nom' => 'Fassapel Centre', 'code' => 'FAS-001'],
                ['nom' => 'Balla', 'code' => 'FAS-002'],
                ['nom' => 'Guitté', 'code' => 'FAS-003']
            ],
            'Kossouka' => [
                ['nom' => 'Kossouka Centre', 'code' => 'KOS-001'],
                ['nom' => 'Rollo', 'code' => 'KOS-002'],
                ['nom' => 'Diarabakoko', 'code' => 'KOS-003']
            ],
            'La-Todin' => [
                ['nom' => 'La-Todin Centre', 'code' => 'LAT-001'],
                ['nom' => 'Koémzeurdo', 'code' => 'LAT-002'],
                ['nom' => 'Bilanga', 'code' => 'LAT-003']
            ],
            'Namissiguima' => [
                ['nom' => 'Namissiguima Centre', 'code' => 'NAM-001'],
                ['nom' => 'Cissé', 'code' => 'NAM-002'],
                ['nom' => 'Toulou', 'code' => 'NAM-003']
            ],
            'Ouahigouya (Rurale)' => [
                ['nom' => 'Kalamba', 'code' => 'OUR-001'],
                ['nom' => 'Bokin', 'code' => 'OUR-002'],
                ['nom' => 'Zam', 'code' => 'OUR-003']
            ],
            'Seguenega' => [
                ['nom' => 'Seguenega Centre', 'code' => 'SEG-001'],
                ['nom' => 'Togoma', 'code' => 'SEG-002'],
                ['nom' => 'Kirsi', 'code' => 'SEG-003']
            ],
            'Tangaye' => [
                ['nom' => 'Tangaye Centre', 'code' => 'TAN-001'],
                ['nom' => 'Gasseliki', 'code' => 'TAN-002'],
                ['nom' => 'Vallée', 'code' => 'TAN-003']
            ],
            'Zogore' => [
                ['nom' => 'Zogore Centre', 'code' => 'ZOG-001'],
                ['nom' => 'Pensy', 'code' => 'ZOG-002'],
                ['nom' => 'Kaya Peulh', 'code' => 'ZOG-003']
            ]
        ];

        $totalVillages = 0;

        foreach ($villagesParCommune as $nomCommune => $villages) {
            $commune = Commune::where('nom', $nomCommune)->first();
            
            if (!$commune) {
                $this->command->warn("⚠️  Commune '{$nomCommune}' non trouvée. Assurez-vous d'avoir exécuté CommuneSeeder en premier.");
                continue;
            }

            foreach ($villages as $villageData) {
                Village::firstOrCreate(
                    ['code' => $villageData['code']],
                    array_merge($villageData, ['commune_id' => $commune->id])
                );
                $totalVillages++;
            }
        }

        $this->command->info("{$totalVillages} villages créés avec succès dans les communes du Yadéga !");
    }
}
