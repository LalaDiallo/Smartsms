<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Marketing',
            'slug' => 'marketing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Récupérer une langue existante
        $languageId = optional(DB::table('languages')->where('code', 'fr')->first())->id;

        // Récupérer une catégorie existante (ex: "Marketing")
        $categoryId = optional(DB::table('categories')->where('slug', 'marketing')->first())->id ?? 1;

        // Récupérer un user existant (ex: Super Admin ID 1)
        $userId = DB::table('clients')->where('id', 1)->value('id') ?? null;

        DB::table('templates')->insert([
            [
                'name' => 'Promotion Générale',
                'extrait' => 'Promo exceptionnelle sur vos prochains achats',
                'channel' => 'email',
                'content' => 'Profitez de {discount}% sur votre prochain achat ! Code : {code}',
                'branding_logo' => '/logos/xyz_logo.png',
                'branding_colors' => json_encode(['primary' => '#FF5733', 'secondary' => '#C70039']),
                'sector' => 'Commerce',
                'language_id' => $languageId,
                'category_id' => $categoryId,
                'client_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alerte Urgente',
                'extrait' => 'Message urgent pour vos clients',
                'channel' => 'sms',
                'content' => 'Alerte : {message}. Répondez pour plus d’infos.',
                'branding_logo' => null,
                'branding_colors' => null,
                'sector' => 'Santé',
                'language_id' => $languageId,
                'category_id' => $categoryId,
                'client_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
