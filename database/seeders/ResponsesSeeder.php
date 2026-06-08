<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ResponsesSeeder extends Seeder
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

        // Récupérer un message existant ou en créer un
        $messageId = DB::table('messages')->inRandomOrder()->value('id');
        if (!$messageId) {
            // Si aucun message, créer une campagne et un message
            $campagnesId = DB::table('campagnes')->insertGetId([
                'name'        => 'Campagne Réponse Auto ' . strtoupper($faker->lexify('???')),
                'description' => 'Campagne générée pour tester les réponses',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $messageId = DB::table('messages')->insertGetId([
                'campagnes_id' => $campagnesId,
                'contact_id'   => $contactId,
                'content'      => 'Message test pour la réponse',
                'sent_at'      => now(),
                'status'       => 'delivered',
                'channel'      => $faker->randomElement(['sms', 'whatsapp']),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Insérer 2 réponses
        DB::table('responses')->insert([
            [
                'message_id'  => $messageId,
                'contact_id'  => $contactId,
                'content'     => 'Merci pour l’offre ! Je vais en profiter.',
                'received_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'message_id'  => $messageId,
                'contact_id'  => $contactId,
                'content'     => 'STOP',
                'received_at' => now()->addHour(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
