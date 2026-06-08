<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LoyaltyProgramsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insérer des programmes de fidélité
        DB::table('programs')->insert([
            [
                'name' => 'Programme Or',
                'description' => 'Gagnez des points pour chaque achat et échangez-les contre des réductions.',
                'points_per_action' => 10,
                'reward_type' => 'discount',
                'reward_value' => 5.00,
                'created_at' => now(),
            ],
            [
                'name' => 'Programme Cadeau',
                'description' => 'Collectez des points pour recevoir des cadeaux exclusifs.',
                'points_per_action' => 15,
                'reward_type' => 'gift',
                'reward_value' => null,
                'created_at' => now(),
            ],
            [
                'name' => 'Programme Cashback',
                'description' => 'Obtenez du cashback sur vos actions.',
                'points_per_action' => 5,
                'reward_type' => 'cash',
                'reward_value' => 2.50,
                'created_at' => now(),
            ],
        ]);
    }
}
