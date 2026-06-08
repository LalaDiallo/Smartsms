<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class MessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Récupérer un contact existant ou en créer un
        $contactId = DB::table('contacts')->inRandomOrder()->value('id');
        if (!$contactId) {
            $contactId = DB::table('contacts')->insertGetId([
                'first_name'        => $faker->firstName,
                'last_name'         => $faker->lastName,
                'email'             => $faker->unique()->safeEmail,
                'phone'             => $faker->unique()->phoneNumber,
                'region'            => $faker->city,
                'preferred_channel' => $faker->randomElement(['sms', 'whatsapp', 'email', 'push']),
                'status'            => $faker->randomElement(['active', 'inactive']),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // Récupérer une campagne existante ou en créer une
        $campagnesId = DB::table('campagnes')->inRandomOrder()->value('id');
        if (!$campagnesId) {
            $campagnesId = DB::table('campagnes')->insertGetId([
                'name'        => 'Campagne Promotionnelle ' . strtoupper($faker->lexify('???')),
                'description' => 'Offre spéciale générée automatiquement',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Insérer 5 messages aléatoires
        for ($i = 0; $i < 5; $i++) {
            DB::table('messages')->insert([
                'campagnes_id' => $campagnesId,
                'contact_id'   => $contactId,
                'content'      => $faker->sentence(8),
                'sent_at'      => $faker->optional()->dateTimeBetween('-1 week', 'now'),
                'status'       => $faker->randomElement(['sent', 'delivered', 'failed']),
                'channel'      => $faker->randomElement(['sms', 'whatsapp', 'email', 'push']),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
