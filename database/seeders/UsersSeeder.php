<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le super_admin unique
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Password123!'),
            'role' => 'super_admin',
            'client_id' => 1, // n'est lié à aucune entreprise
            'status' => 'active',
            'phone' => '+224600000000',
            'email_verified_at' => now(),
            'activation_token' => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Récupérer toutes les entreprises
        $clients = DB::table('clients')->get();

        // Définir les autres rôles
        $roles = ['admin', 'observateur', 'manager', 'operator', 'developer', 'gouvernement', 'responsable_regional'];

        foreach ($clients as $client) {
            foreach ($roles as $role) {
                DB::table('users')->insert([
                    'name' => ucfirst($role) . ' ' . $client->company_name,
                    'email' => strtolower($role . '.' . str_replace(' ', '', $client->company_name)) . '@example.com',
                    'password' => Hash::make('Password123!'),
                    'role' => $role,
                    'client_id' => $client->id,
                    'status' => 'active',
                    'profil' => 'default.png',
                    'phone' => '+2246' . rand(10000000, 99999999),
                    'email_verified_at' => now(),
                    'activation_token' => null,
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
