<?php

namespace Database\Seeders;

use App\Models\SpamRules;
use Illuminate\Database\Seeder;

class SpamRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |-------------------------------------------------------------
        | Règle 1 : “Promo agressive” – type keyword
        |-------------------------------------------------------------
        */
        $promoRule = SpamRules::create([
            'nom_regle'   => 'Promo agressive',
            'type'        => 'keyword',
            'action'      => 'quarantine',
            'severity'    => 'high',
            'description' => 'Bloque les messages très promotionnels',
            'auto_learn'  => false,
            'status'      => 'active',
        ]);

        $promoRule->keywords()->createMany([
            ['keyword' => 'GRATUIT'],
            ['keyword' => 'URGENT'],
            ['keyword' => 'JACKPOT'],
        ]);

        // ✅ Channels (solution A)
        $promoRule->channels()->createMany([
            ['channel' => 'sms'],
            ['channel' => 'email'],
        ]);

        /*
        |-------------------------------------------------------------
        | Règle 2 : “Expéditeur suspect” – type sender
        |-------------------------------------------------------------
        */
        $senderRule = SpamRules::create([
            'nom_regle'   => 'Expéditeur suspect',
            'type'        => 'sender',
            'action'      => 'flag',
            'severity'    => 'medium',
            'description' => 'Signale les domaines d’expéditeurs douteux',
            'auto_learn'  => true,
            'status'      => 'active',
        ]);

        $senderRule->senderDomains()->createMany([
            ['domain' => 'promo.example.com'],
            ['domain' => 'superdeal.spam.io'],
        ]);

        $senderRule->channels()->createMany([
            ['channel' => 'sms'],
            ['channel' => 'email'],
        ]);

        /*
        |-------------------------------------------------------------
        | Règle 3 : “Trop grande fréquence” – type frequency
        |-------------------------------------------------------------
        */
        $frequencyRule = SpamRules::create([
            'nom_regle'   => 'Trop grande fréquence',
            'type'        => 'frequency',
            'action'      => 'block',
            'severity'    => 'critical',
            'description' => 'Bloque les expéditeurs envoyant >100 SMS/minute',
            'auto_learn'  => false,
            'status'      => 'active',
        ]);

        // ✅ fréquence (et non contentRules)
        $frequencyRule->frequence()->create([
            'frequency_limit'  => 100,
            'frequency_period' => 'minute',
        ]);

        $frequencyRule->channels()->create([
        'channel' => 'sms'
        ]);
    }
}
