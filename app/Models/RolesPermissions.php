<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolesPermissions extends Model
{
    protected $table = 'roles_permissions';
    protected $fillable = [
        'role', 'peut_creer_utilisateur', 'peut_modifier_utilisateur', 'peut_supprimer_utilisateur',
        'peut_attribuer_permissions', 'peut_integrer_outils_externes', 'peut_gerer_routage_sms',
        'peut_configurer_parametres_globaux', 'peut_generer_journaux_audit', 'peut_gerer_budget',
        'peut_definir_alertes_budget', 'peut_creer_campagne', 'peut_envoyer_campagne',
        'peut_partager_brouillon', 'peut_approuver_campagne', 'peut_voir_analytiques',
        'peut_segmenter_audience', 'peut_personnaliser_contenu', 'peut_voir_analytiques_regionales',
        'peut_localiser_contenu', 'peut_voir_campagnes', 'peut_voir_detail_campagnes', 'peut_voir_budget',
        'peut_ajouter_branding', 'peut_recharger_credits', 'peut_executer_campagne',
        'peut_modifier_details_mineurs_campagne', 'peut_voir_reponses_clients',
        'peut_recevoir_notifications', 'peut_acceder_api', 'peut_voir_analytiques_api',
        'peut_developper_plugins', 'peut_envoyer_alertes_publiques', 'peut_voir_tableau_impact',
        'peut_collecter_retours',
    ];
}
