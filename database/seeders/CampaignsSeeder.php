<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->whereIn('role', ['admin', 'manager', 'operator', 'developer'])->get();
        $channels = ['sms', 'whatsapp', 'email', 'push'];
        $statuses = ['brouillon', 'programmer', 'attente', 'terminer'];
        $regions = ['Conakry', 'Kindia', 'Labé', 'Nzérékoré', 'Kankan'];

        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $startDate = Carbon::now()->addDays(rand(0, 10))->format('Y-m-d H:i:s');
            $endDate = Carbon::parse($startDate)->addDays(rand(1, 5))->format('Y-m-d H:i:s');

            DB::table('campagnes')->insert([
                'user_id' => $user->id,
                'name' => 'Campagne ' . $i,
                'status' => $statuses[array_rand($statuses)],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'region' => $regions[array_rand($regions)],
                'channel' => $channels[array_rand($channels)],
                'template_id' => null, // peut être remplacé par un id existant si tu as des templates
                'settings' => json_encode([
                    'tracking' => true,
                    'notifications' => ['sms' => true, 'email' => true],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
