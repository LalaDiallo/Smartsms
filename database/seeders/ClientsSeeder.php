<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'plan_id'               => 1, // Plan Basic / Freemium
                'company_name'          => 'AlphaTech Solutions',
                'contact_name'          => 'Alpha Diallo',
                'email'                 => 'contact@alphatech.com',
                'phone'                 => '+224612345678',
                'website'               => 'https://alphatech.com',
                'forme_juridique'       => 'SARL',
                'numero_immatriculation'=> 'RCCM/GN/CKY/12345A/2023',
                'city'                  => 'Conakry',
                'country'               => 'Guinée',
                'industry'              => 'Technologie',
                'status'                => 'active',
                'joined_at'             => Carbon::now()->subMonths(12),
                'last_activity'         => Carbon::now()->subDays(2),
                'contract_end'          => Carbon::now()->addMonths(6),
                'monthly_revenue'       => 85000.00,    // en GNF
                'total_spent'           => 1020000.00,
                'messages_sent'         => 45000,
                'satisfaction'          => 4.7,
                'support_tickets'       => 3,
                'features'              => json_encode(['api_access', 'analytics_basic']),
                'logo'                  => 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=100',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'plan_id'               => 2, // Plan Pro
                'company_name'          => 'Global Industries',
                'contact_name'          => 'Mamadou Bah',
                'email'                 => 'info@globalindustries.com',
                'phone'                 => '+224622345678',
                'website'               => 'https://globalindustries.com',
                'forme_juridique'       => 'SA',
                'numero_immatriculation'=> 'RCCM/GN/KD/67890B/2022',
                'city'                  => 'Kindia',
                'country'               => 'Guinée',
                'industry'              => 'Industrie',
                'status'                => 'active',
                'joined_at'             => Carbon::now()->subMonths(8),
                'last_activity'         => Carbon::now()->subHours(5),
                'contract_end'          => Carbon::now()->addYear(),
                'monthly_revenue'       => 250000.00,
                'total_spent'           => 2000000.00,
                'messages_sent'         => 120000,
                'satisfaction'          => 4.9,
                'support_tickets'       => 1,
                'features'              => json_encode(['api_access', 'white_label', 'priority_support']),
                'logo'                  => 'https://images.pexels.com/photos/3184339/pexels-photo-3184339.jpeg?auto=compress&cs=tinysrgb&w=100',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'plan_id'               => 1,
                'company_name'          => 'EcoFarm Agriculture',
                'contact_name'          => 'Aissatou Camara',
                'email'                 => 'contact@ecofarm.com',
                'phone'                 => '+224633345678',
                'website'               => null,
                'forme_juridique'       => 'SARL',
                'numero_immatriculation'=> 'RCCM/GN/LB/11223C/2024',
                'city'                  => 'Labé',
                'country'               => 'Guinée',
                'industry'              => 'Agriculture',
                'status'                => 'essaie',
                'joined_at'             => Carbon::now()->subDays(15),
                'last_activity'         => Carbon::now()->subDays(1),
                'contract_end'          => Carbon::now()->addDays(15),
                'monthly_revenue'       => 45000.00,
                'total_spent'           => 45000.00,
                'messages_sent'         => 8000,
                'satisfaction'          => 4.2,
                'support_tickets'       => 0,
                'features'              => null,
                'logo'                  => null,
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'plan_id'               => 3, // Plan Enterprise
                'company_name'          => 'Nova Transport',
                'contact_name'          => 'Ibrahima Keita',
                'email'                 => 'support@novatransport.com',
                'phone'                 => '+224644345678',
                'website'               => 'https://novatransport.com',
                'forme_juridique'       => 'SA',
                'numero_immatriculation'=> 'RCCM/GN/NZK/44556D/2021',
                'city'                  => 'Nzérékoré',
                'country'               => 'Guinée',
                'industry'              => 'Transport & Logistique',
                'status'                => 'active',
                'joined_at'             => Carbon::now()->subYears(2),
                'last_activity'         => Carbon::now(),
                'contract_end'          => Carbon::now()->addYears(2),
                'monthly_revenue'       => 750000.00,
                'total_spent'           => 18000000.00,
                'messages_sent'         => 500000,
                'satisfaction'          => 4.8,
                'support_tickets'       => 8,
                'features'              => json_encode(['dedicated_support', 'custom_integration', 'unlimited_sms']),
                'logo'                  => 'https://images.pexels.com/photos/3184338/pexels-photo-3184338.jpeg?auto=compress&cs=tinysrgb&w=100',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
            [
                'plan_id'               => 2,
                'company_name'          => 'SmartBuild Constructions',
                'contact_name'          => 'Fatoumata Sylla',
                'email'                 => 'contact@smartbuild.com',
                'phone'                 => '+224655345678',
                'website'               => 'https://smartbuild.com',
                'forme_juridique'       => 'SARL',
                'numero_immatriculation'=> 'RCCM/GN/KD/77889E/2023',
                'city'                  => 'Kindia',
                'country'               => 'Guinée',
                'industry'              => 'Construction',
                'status'                => 'suspended',
                'joined_at'             => Carbon::now()->subMonths(6),
                'last_activity'         => Carbon::now()->subMonths(2),
                'contract_end'          => Carbon::now()->subMonths(1),
                'monthly_revenue'       => 0.00,
                'total_spent'           => 900000.00,
                'messages_sent'         => 35000,
                'satisfaction'          => 3.1,
                'support_tickets'       => 12,
                'features'              => json_encode(['api_access', 'analytics_pro']),
                'logo'                  => 'https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=100',
                'created_at'            => now(),
                'updated_at'            => now(),
            ],
        ];

        DB::table('clients')->insert($clients);
    }
}
