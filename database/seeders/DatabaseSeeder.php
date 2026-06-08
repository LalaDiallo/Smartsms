<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ── Référentiels de base ─────────────────────────────────────
            PlanSeeder::class,              // Plans legacy (modèle Plan)
            RolesPermissionsSeeder::class,
            LanguagesSeeder::class,

            // ── Clients & Utilisateurs ───────────────────────────────────
            ClientsSeeder::class,
            UsersSeeder::class,

            // ── Abonnements (dépend de ClientsSeeder + migrations plans) ─
            SubscriptionSeeder::class,

            // ── Contenu métier ───────────────────────────────────────────
            TemplatesSeeder::class,
            ContactsSeeder::class,
            CampaignsSeeder::class,
            MessagesSeeder::class,
            ResponsesSeeder::class,
            EventsSeeder::class,
            OperatorsSeeder::class,
            LoyaltyProgramsSeeder::class,
            SpamRulesSeeder::class,
            NotificationSeeder::class,
            BrandingSeeder::class,
        ]);
    }
}
