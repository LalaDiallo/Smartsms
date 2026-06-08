<?php

namespace Database\Seeders;

use App\Models\Contacts;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = DB::table('clients')->get();

        $preferred_channels = ['sms', 'whatsapp', 'email', 'push'];
        $statuses = ['active', 'inactive', 'employes'];
        $regions = ['Conakry', 'Kindia', 'Labé', 'Nzérékoré', 'Kankan'];

        foreach ($clients as $client) {
            for ($i = 1; $i <= 10; $i++) {
                $firstName = 'Contact' . $i;
                $lastName = 'Entreprise' . $client->id;
                $email = strtolower($firstName . '.' . $lastName . '@example.com');
                $phone = '+2246' . rand(10000000, 99999999);

                DB::table('contacts')->insert([
                    'client_id' => $client->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'region' => $regions[array_rand($regions)],
                    'preferred_channel' => $preferred_channels[array_rand($preferred_channels)],
                    'is_spammer' => false,
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
