<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrateur',
                'description' => 'Accès complet à toutes les fonctionnalités du système',
                'permissions' => json_encode(['*']) // Tous les permissions
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Superviseur',
                'description' => 'Peut valider les interventions et consulter tous les rapports',
                'permissions' => json_encode(['view_all', 'validate_intervention', 'generate_reports'])
            ],
            [
                'name' => 'technician',
                'display_name' => 'Technicien',
                'description' => 'Peut créer et modifier ses propres interventions',
                'permissions' => json_encode(['view_own', 'create_intervention', 'update_own_intervention'])
            ],
            [
                'name' => 'storekeeper',
                'display_name' => 'Magasinier',
                'description' => 'Gère le stock des pièces de rechange',
                'permissions' => json_encode(['view_parts', 'manage_stock', 'assign_parts'])
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Consultant',
                'description' => 'Accès en lecture seule aux données',
                'permissions' => json_encode(['view_all'])
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'display_name' => $role['display_name'],
                    'description' => $role['description'],
                    'permissions' => $role['permissions']
                ]
            );
        }

        $this->command->info('Rôles créés avec succès !');
    }
}
