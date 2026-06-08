<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles_permissions', function (Blueprint $table) {
            $table->id();
            $table->enum('role', [
                'super_admin',
                'admin',
                'manager',
                'operator',
                'developer',
                'gouvernement',
                'responsable_regional',
                'observateur'
            ])->unique();

            $table->boolean('peut_creer_utilisateur')->default(false);
            $table->boolean('peut_modifier_utilisateur')->default(false);
            $table->boolean('peut_supprimer_utilisateur')->default(false);
            $table->boolean('peut_attribuer_permissions')->default(false);
            $table->boolean('peut_integrer_outils_externes')->default(false);
            $table->boolean('peut_gerer_routage_sms')->default(false);
            $table->boolean('peut_configurer_parametres_globaux')->default(false);
            $table->boolean('peut_generer_journaux_audit')->default(false);
            $table->boolean('peut_gerer_budget')->default(false);
            $table->boolean('peut_definir_alertes_budget')->default(false);
            $table->boolean('peut_creer_campagne')->default(false);
            $table->boolean('peut_gerer_campagne')->default(false);
            $table->boolean('peut_envoyer_campagne')->default(false);
            $table->boolean('peut_partager_brouillon')->default(false);
            $table->boolean('peut_approuver_campagne')->default(false);
            $table->boolean('peut_voir_analytiques')->default(false);
            $table->boolean('peut_segmenter_audience')->default(false);
            $table->boolean('peut_personnaliser_contenu')->default(false);
            $table->boolean('peut_voir_analytiques_regionales')->default(false);
            $table->boolean('peut_localiser_contenu')->default(false);
            $table->boolean('peut_voir_campagnes')->default(false);
            $table->boolean('peut_voir_detail_campagnes')->default(false);
            $table->boolean('peut_voir_budget')->default(false);
            $table->boolean('peut_ajouter_branding')->default(false);
            $table->boolean('peut_recharger_credits')->default(false);
            $table->boolean('peut_executer_campagne')->default(false);
            $table->boolean('peut_modifier_details_mineurs_campagne')->default(false);
            $table->boolean('peut_voir_reponses_clients')->default(false);
            $table->boolean('peut_recevoir_notifications')->default(false);
            $table->boolean('peut_acceder_api')->default(false);
            $table->boolean('peut_voir_analytiques_api')->default(false);
            $table->boolean('peut_developper_plugins')->default(false);
            $table->boolean('peut_envoyer_alertes_publiques')->default(false);
            $table->boolean('peut_voir_tableau_impact')->default(false);
            $table->boolean('peut_collecter_retours')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_permissions');
    }
};
