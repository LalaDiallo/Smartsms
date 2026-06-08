<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branding;
use App\Models\Clients;

class BrandingSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Clients::take(5)->get();

        if ($clients->count() < 5) {
            $this->command->warn('Il faut au moins 5 clients pour créer 5 brandings.');
            return;
        }

        $brandingsData = [
            [
                'brand_name' => 'SmartSMS',
                'primary_color' => '#0A1AFF',
                'secondary_color' => '#F5F7FB',
                'accent_color' => '#FFB703',
                'font_family' => 'Inter',
                'is_active' => false,
            ],
            [
                'brand_name' => 'FastPay',
                'primary_color' => '#0FB9B1',
                'secondary_color' => '#EAFDFC',
                'accent_color' => '#20BF6B',
                'font_family' => 'Poppins',
                'is_active' => false,
            ],
            [
                'brand_name' => 'AfriConnect',
                'primary_color' => '#F39C12',
                'secondary_color' => '#FFF3E0',
                'accent_color' => '#D35400',
                'font_family' => 'Montserrat',
                'is_active' => false,
            ],
            [
                'brand_name' => 'NeoCom',
                'primary_color' => '#6C5CE7',
                'secondary_color' => '#F1F0FF',
                'accent_color' => '#00CEC9',
                'font_family' => 'Roboto',
                'is_active' => false,
            ],
            [
                'brand_name' => 'CloudBiz',
                'primary_color' => '#2D3436',
                'secondary_color' => '#F8F9FA',
                'accent_color' => '#0984E3',
                'font_family' => 'Nunito',
                'is_active' => false,
            ],
        ];

        foreach ($clients as $index => $client) {
            Branding::create([
                'client_id' => $client->id,
                'brand_name' => $brandingsData[$index]['brand_name'],
                'primary_color' => $brandingsData[$index]['primary_color'],
                'secondary_color' => $brandingsData[$index]['secondary_color'],
                'accent_color' => $brandingsData[$index]['accent_color'],
                'font_family' => $brandingsData[$index]['font_family'],
                'description' => 'Branding de démonstration',
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => 1, // admin
                'is_active' => $brandingsData[$index]['is_active'],
            ]);
        }

        $this->command->info('✅ 5 brandings créés pour 5 clients.');
    }
}
