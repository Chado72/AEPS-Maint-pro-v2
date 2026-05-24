<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $techRole = Role::where('name', 'technician')->first();
        $supervisorRole = Role::where('name', 'supervisor')->first();
        $storekeeperRole = Role::where('name', 'storekeeper')->first();

        $users = [
            [
                'name' => 'Administrateur Système',
                'email' => 'admin@onea.bf',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole?->id,
                'phone' => '+226 00 00 00 00',
                'is_active' => true
            ],
            [
                'name' => 'Superviseur Provincial',
                'email' => 'supervisor@onea.bf',
                'password' => Hash::make('supervisor123'),
                'role_id' => $supervisorRole?->id,
                'phone' => '+226 00 00 00 01',
                'is_active' => true
            ],
            [
                'name' => 'Technicien Principal',
                'email' => 'technicien@onea.bf',
                'password' => Hash::make('tech123'),
                'role_id' => $techRole?->id,
                'phone' => '+226 00 00 00 02',
                'is_active' => true
            ],
            [
                'name' => 'Magasinier Central',
                'email' => 'magasin@onea.bf',
                'password' => Hash::make('magasin123'),
                'role_id' => $storekeeperRole?->id,
                'phone' => '+226 00 00 00 03',
                'is_active' => true
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Utilisateurs créés avec succès !');
        $this->command->info('Emails de connexion :');
        $this->command->info('  - admin@onea.bf / admin123');
        $this->command->info('  - supervisor@onea.bf / supervisor123');
        $this->command->info('  - technicien@onea.bf / tech123');
        $this->command->info('  - magasin@onea.bf / magasin123');
    }
}
