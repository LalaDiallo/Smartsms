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
        // Permissions corrigées par rôle selon la logique métier SmartSMS
        $updates = [

            'admin' => [
                // Manquait
                'peut_developper_plugins' => 1,
            ],

            'manager' => [
                // Pour accéder aux paramètres et voir les analytics régionales
                'peut_configurer_parametres_globaux'  => 1,
                'peut_voir_analytiques_regionales'    => 1,
                // Recharge SMS disponible
                'peut_recharger_credits'              => 1,
                'peut_voir_budget'                    => 1,
                // Voir les réponses et retours
                'peut_voir_reponses_clients'          => 1,
                'peut_collecter_retours'              => 1,
                'peut_recevoir_notifications'         => 1,
            ],

            'operator' => [
                // Exécution de campagnes
                'peut_executer_campagne'              => 1,
                'peut_voir_detail_campagnes'          => 1,
                'peut_modifier_details_mineurs_campagne' => 1,
                // Quota et recharge
                'peut_voir_budget'                    => 1,
                'peut_recharger_credits'              => 1,
                // Réponses et retours
                'peut_voir_reponses_clients'          => 1,
                'peut_collecter_retours'              => 1,
                'peut_recevoir_notifications'         => 1,
            ],

            'developer' => [
                // Pour les analytics API et le débogage
                'peut_voir_analytiques'               => 1,
                'peut_voir_analytiques_api'           => 1,
                'peut_voir_campagnes'                 => 1,
                'peut_voir_contacts'                  => 1,
                'peut_voir_detail_campagnes'          => 1,
                // Quota et recharge
                'peut_voir_budget'                    => 1,
                'peut_recharger_credits'              => 1,
                // Intégrations
                'peut_integrer_outils_externes'       => 1,
                'peut_configurer_parametres_globaux'  => 1,
                'peut_recevoir_notifications'         => 1,
            ],

            'gouvernement' => [
                // Analytics régionales et alertes publiques
                'peut_voir_analytiques_regionales'    => 1,
                'peut_envoyer_alertes_publiques'      => 1,
                // Accès lecture campagnes et contacts
                'peut_voir_campagnes'                 => 1,
                'peut_voir_contacts'                  => 1,
                'peut_voir_detail_campagnes'          => 1,
                // Quota
                'peut_voir_budget'                    => 1,
                'peut_recharger_credits'              => 1,
                'peut_recevoir_notifications'         => 1,
                'peut_collecter_retours'              => 1,
            ],

            'responsable_regional' => [
                // Analytics — clé manquante causant 403
                'peut_voir_analytiques'               => 1,
                'peut_voir_analytiques_regionales'    => 1,
                // Accès lecture
                'peut_voir_campagnes'                 => 1,
                'peut_voir_contacts'                  => 1,
                'peut_voir_detail_campagnes'          => 1,
                // Quota et recharge
                'peut_voir_budget'                    => 1,
                'peut_recharger_credits'              => 1,
                // Retours et réponses
                'peut_voir_reponses_clients'          => 1,
                'peut_collecter_retours'              => 1,
                'peut_recevoir_notifications'         => 1,
                // Tableau d'impact (déjà présent mais sécurité)
                'peut_voir_tableau_impact'            => 1,
            ],

            'observateur' => [
                // Lecture seule — campagnes et contacts
                'peut_voir_campagnes'                 => 1,
                'peut_voir_contacts'                  => 1,
                'peut_voir_detail_campagnes'          => 1,
                // Quota
                'peut_voir_budget'                    => 1,
                'peut_recharger_credits'              => 1,
                // Retours
                'peut_voir_reponses_clients'          => 1,
                'peut_collecter_retours'              => 1,
                'peut_recevoir_notifications'         => 1,
            ],
        ];

        foreach ($updates as $role => $perms) {
            \Illuminate\Support\Facades\DB::table('roles_permissions')
                ->where('role', $role)
                ->update($perms);
        }
    }

    public function down(): void
    {
        // Difficile à reverter sans snapshot — on ne modifie pas le down()
    }
};
