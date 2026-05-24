<?php

namespace Database\Seeders;

use App\Models\Commune;
use Illuminate\Database\Seeder;

class CommuneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Communes de la province du Yadéga (Région du Nord, Burkina Faso)
        $communes = [
            ['nom' => 'Ouahigouya', 'code' => 'YAD-01', 'superficie_km2' => 847],
            ['nom' => 'Bourzanga', 'code' => 'YAD-02', 'superficie_km2' => 623],
            ['nom' => 'Fassapel', 'code' => 'YAD-03', 'superficie_km2' => 412],
            ['nom' => 'Kossouka', 'code' => 'YAD-04', 'superficie_km2' => 358],
            ['nom' => 'La-Todin', 'code' => 'YAD-05', 'superficie_km2' => 295],
            ['nom' => 'Namissiguima', 'code' => 'YAD-06', 'superficie_km2' => 534],
            ['nom' => 'Ouahigouya (Rurale)', 'code' => 'YAD-07', 'superficie_km2' => 421],
            ['nom' => 'Seguenega', 'code' => 'YAD-08', 'superficie_km2' => 389],
            ['nom' => 'Tangaye', 'code' => 'YAD-09', 'superficie_km2' => 276],
            ['nom' => 'Zogore', 'code' => 'YAD-10', 'superficie_km2' => 312]
        ];

        foreach ($communes as $communeData) {
            Commune::firstOrCreate(
                ['code' => $communeData['code']],
                $communeData
            );
        }

        $this->command->info(count($communes) . ' communes de la province du Yadéga créées avec succès !');
    }
}
