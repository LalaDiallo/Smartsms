<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insérer des langues
        DB::table('languages')->insert([
            [
                'name' => 'Wolof',
                'code' => 'wo',
                'created_at' => now(),
            ],
            [
                'name' => 'Lingala',
                'code' => 'ln',
                'created_at' => now(),
            ],
            [
                'name' => 'Swahili',
                'code' => 'sw',
                'created_at' => now(),
            ],
            [
                'name' => 'Français',
                'code' => 'fr',
                'created_at' => now(),
            ],
            [
                'name' => 'Anglais',
                'code' => 'en',
                'created_at' => now(),
            ],
        ]);
    }
}
