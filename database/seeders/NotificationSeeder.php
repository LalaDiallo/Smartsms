<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notification_settings')->insert([
            [
                'client_id'   => 1,
                'name'        => 'Nouvelles campagnes',
                'description' => 'Recevoir un email lors de la création d\'une campagne',
                'enabled'     => true,
            ],
            [
                'client_id'   => 1,
                'name'        => 'Rapports hebdomadaires',
                'description' => 'Résumé des performances chaque lundi',
                'enabled'     => true,
            ],
            [
                'client_id'   => 1,
                'name'        => 'Campagnes terminées',
                'description' => 'Notification quand une campagne se termine',
                'enabled'     => true,
            ],
            [
                'client_id'   => 1,
                'name'        => 'Nouveaux contacts',
                'description' => 'Notification pour chaque nouveau contact',
                'enabled'     => true,
            ],
        ]);
    }
}
