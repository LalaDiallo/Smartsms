<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $all = [
            'peut_creer_utilisateur'              => true,
            'peut_modifier_utilisateur'           => true,
            'peut_supprimer_utilisateur'          => true,
            'peut_attribuer_permissions'          => true,
            'peut_integrer_outils_externes'       => true,
            'peut_gerer_routage_sms'              => true,
            'peut_configurer_parametres_globaux'  => true,
            'peut_generer_journaux_audit'         => true,
            'peut_gerer_budget'                   => true,
            'peut_definir_alertes_budget'         => true,
            'peut_creer_campagne'                 => true,
            'peut_gerer_campagne'                 => true,
            'peut_supprimer_campagne'             => true,
            'peut_envoyer_campagne'               => true,
            'peut_partager_brouillon'             => true,
            'peut_approuver_campagne'             => true,
            'peut_voir_analytiques'               => true,
            'peut_segmenter_audience'             => true,
            'peut_personnaliser_contenu'          => true,
            'peut_voir_analytiques_regionales'    => true,
            'peut_localiser_contenu'              => true,
            'peut_voir_campagnes'                 => true,
            'peut_voir_detail_campagnes'          => true,
            'peut_voir_budget'                    => true,
            'peut_ajouter_branding'               => true,
            'peut_recharger_credits'              => true,
            'peut_executer_campagne'              => true,
            'peut_modifier_details_mineurs_campagne' => true,
            'peut_voir_reponses_clients'          => true,
            'peut_recevoir_notifications'         => true,
            'peut_acceder_api'                    => true,
            'peut_voir_analytiques_api'           => true,
            'peut_developper_plugins'             => true,
            'peut_envoyer_alertes_publiques'      => true,
            'peut_voir_tableau_impact'            => true,
            'peut_collecter_retours'              => true,
            'peut_voir_contacts'                  => true,
            'peut_gerer_contacts'                 => true,
        ];

        $none = array_map(fn() => false, $all);

        $roles = [
            'super_admin' => $all,

            'admin' => array_merge($all, [
                'peut_developper_plugins' => false,
            ]),

            'manager' => array_merge($none, [
                'peut_creer_utilisateur'           => true,
                'peut_modifier_utilisateur'        => true,
                'peut_generer_journaux_audit'      => true,
                'peut_gerer_budget'                => true,
                'peut_definir_alertes_budget'      => true,
                'peut_voir_budget'                 => true,
                'peut_recharger_credits'           => true,
                'peut_creer_campagne'              => true,
                'peut_gerer_campagne'              => true,
                'peut_supprimer_campagne'          => true,
                'peut_envoyer_campagne'            => true,
                'peut_partager_brouillon'          => true,
                'peut_approuver_campagne'          => true,
                'peut_executer_campagne'           => true,
                'peut_modifier_details_mineurs_campagne' => true,
                'peut_voir_campagnes'              => true,
                'peut_voir_detail_campagnes'       => true,
                'peut_voir_analytiques'            => true,
                'peut_segmenter_audience'          => true,
                'peut_personnaliser_contenu'       => true,
                'peut_localiser_contenu'           => true,
                'peut_voir_reponses_clients'       => true,
                'peut_recevoir_notifications'      => true,
                'peut_voir_tableau_impact'         => true,
                'peut_collecter_retours'           => true,
                'peut_voir_contacts'               => true,
                'peut_gerer_contacts'              => true,
            ]),

            'operator' => array_merge($none, [
                'peut_voir_campagnes'              => true,
                'peut_voir_detail_campagnes'       => true,
                'peut_executer_campagne'           => true,
                'peut_modifier_details_mineurs_campagne' => true,
                'peut_voir_reponses_clients'       => true,
                'peut_recevoir_notifications'      => true,
                'peut_voir_contacts'               => true,
            ]),

            'developer' => array_merge($none, [
                'peut_integrer_outils_externes'      => true,
                'peut_configurer_parametres_globaux' => true,
                'peut_recevoir_notifications'        => true,
                'peut_acceder_api'                   => true,
                'peut_voir_analytiques_api'          => true,
                'peut_developper_plugins'            => true,
            ]),

            'gouvernement' => array_merge($none, [
                'peut_voir_analytiques'           => true,
                'peut_voir_reponses_clients'      => true,
                'peut_recevoir_notifications'     => true,
                'peut_envoyer_alertes_publiques'  => true,
                'peut_voir_tableau_impact'        => true,
                'peut_collecter_retours'          => true,
            ]),

            'responsable_regional' => array_merge($none, [
                'peut_voir_campagnes'             => true,
                'peut_voir_detail_campagnes'      => true,
                'peut_voir_analytiques_regionales'=> true,
                'peut_envoyer_alertes_publiques'  => true,
                'peut_voir_tableau_impact'        => true,
                'peut_recevoir_notifications'     => true,
                'peut_voir_contacts'              => true,
            ]),

            'observateur' => array_merge($none, [
                'peut_voir_campagnes'         => true,
                'peut_voir_detail_campagnes'  => true,
                'peut_voir_analytiques'       => true,
                'peut_voir_tableau_impact'    => true,
                'peut_voir_reponses_clients'  => true,
                'peut_recevoir_notifications' => true,
                'peut_voir_contacts'          => true,
            ]),
        ];

        foreach ($roles as $role => $permissions) {
            DB::table('roles_permissions')->updateOrInsert(
                ['role' => $role],
                $permissions
            );
        }
    }
}
