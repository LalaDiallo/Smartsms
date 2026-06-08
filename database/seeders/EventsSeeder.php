<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insérer des événements
        DB::table('events')->insert([
            [
                'name' => 'Fête de l’Indépendance',
                'region' => 'Sénégal',
                'event_date' => '2025-04-04',
                'category' => 'cultural',
                'created_at' => now(),
            ],
            [
                'name' => 'Black Friday',
                'region' => 'Nigeria',
                'event_date' => '2025-11-28',
                'category' => 'commercial',
                'created_at' => now(),
            ],
            [
                'name' => 'Festival de Kinshasa',
                'region' => 'Congo',
                'event_date' => '2025-07-15',
                'category' => 'cultural',
                'created_at' => now(),
            ],
        ]);
    }
}
