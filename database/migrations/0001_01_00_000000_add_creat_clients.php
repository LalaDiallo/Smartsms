<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Relation avec les plans (un client appartient à un plan)
            $table->foreignId('plan_id')
                  ->nullable()
                  ->constrained('plans')
                  ->onDelete('set null');

            // Informations entreprise
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('website')->nullable();
            $table->string('forme_juridique')->nullable();
            $table->string('numero_immatriculation')->nullable();
            $table->string('city');
            $table->string('country', 100)->default('Guinée'); // ou 'France' selon ton contexte
            $table->string('industry');

            // Taille et plan (optionnel : si tu veux garder en plus de la relation plan_id)
            // $table->enum('size', ['startup', 'small', 'medium', 'large', 'enterprise'])->default('startup');
            // $table->enum('plan', ['basic', 'pro', 'enterprise', 'custom'])->default('basic');

            // Statut du client
            $table->enum('status', ['active', 'essaie', 'suspended', 'resilie'])
                  ->default('essaie');

            // Dates importantes
            $table->date('joined_at')->useCurrent();                    // Date d'inscription
            $table->timestamp('last_activity')->nullable();             // Dernière connexion/activité
            $table->date('contract_end')->nullable();                   // Fin de contrat (pour abonnements)

            // Métriques business
            $table->decimal('monthly_revenue', 10, 2)->default(0.00);   // Revenu mensuel généré
            $table->decimal('total_spent', 12, 2)->default(0.00);       // Total dépensé
            $table->integer('messages_sent')->default(0);              // SMS envoyés

            // Satisfaction client
            $table->decimal('satisfaction', 3, 1)->default(4.0);       // Note sur 5 (ex: 4.5)
            $table->integer('support_tickets')->default(0);            // Tickets ouverts

            // Fonctionnalités activées (optionnel : si tu veux stocker des overrides)
            $table->json('features')->nullable();

            // Logo de l'entreprise
            $table->string('logo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
