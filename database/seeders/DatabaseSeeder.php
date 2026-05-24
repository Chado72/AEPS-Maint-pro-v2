<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('=== Démarrage du seeding de la base de données AEPS-Maint Pro ===');
        
        // Ordre important : les dépendances doivent être créées en premier
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CommuneSeeder::class,
            VillageSeeder::class,
            SparePartSeeder::class,
        ]);

        $this->command->info('=== Seeding terminé avec succès ! ===');
        $this->command->info('');
        $this->command->info('📝 Résumé des données créées :');
        $this->command->info('  ✓ 5 rôles utilisateurs');
        $this->command->info('  ✓ 4 utilisateurs de test');
        $this->command->info('  ✓ Communes et villages (à définir dans CommuneSeeder/VillageSeeder)');
        $this->command->info('  ✓ 20 pièces de rechange courantes');
        $this->command->info('');
        $this->command->info('🔐 Identifiants de connexion :');
        $this->command->info('  • Admin : admin@onea.bf / admin123');
        $this->command->info('  • Superviseur : supervisor@onea.bf / supervisor123');
        $this->command->info('  • Technicien : technicien@onea.bf / tech123');
        $this->command->info('  • Magasinier : magasin@onea.bf / magasin123');
    }
}
