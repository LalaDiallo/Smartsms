<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OperatorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insérer des opérateurs
        DB::table('operators')->insert([
            [
                'name' => 'Orange',
                'country' => 'Sénégal',
                'cost_per_sms' => 0.05,
                'reliability_score' => 95.50,
                'created_at' => now(),
            ],
            [
                'name' => 'MTN',
                'country' => 'Nigeria',
                'cost_per_sms' => 0.07,
                'reliability_score' => 90.00,
                'created_at' => now(),
            ],
            [
                'name' => 'Vodacom',
                'country' => 'Congo',
                'cost_per_sms' => 0.06,
                'reliability_score' => 92.75,
                'created_at' => now(),
            ],
        ]);
    }
}
