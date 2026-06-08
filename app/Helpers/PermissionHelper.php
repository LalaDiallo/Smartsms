<?php

namespace App\Helpers;

class PermissionHelper
{
    public static function getPermissionLabels(): array
{
    return [

        // ANALYTIQUES & AUDIT
        'peut_voir_analytiques' => "Voir les indicateurs de performance",
        'peut_voir_analytiques_regionales' => "Voir les analytiques régionales",
        'peut_generer_journaux_audit' => "Générer des rapports d'audit",

        // CAMPAGNES
        'peut_creer_campagne' => "Créer des campagnes",
        'peut_gerer_campagne' => "Gérer les campagnes",
        'peut_supprimer_campagne' => "Supprimer une campagne",
        'peut_envoyer_campagne' => "Envoyer/Lancer une campagne",
        'peut_partager_brouillon' => "Partager des brouillons",
        'peut_approuver_campagne' => "Approuver une campagne",
        'peut_executer_campagne' => "Exécuter une campagne",
        'peut_modifier_details_mineurs_campagne' => "Modifier des détails mineurs",

        // VISIBILITÉ CAMPAGNES
        'peut_voir_campagnes' => "Voir la liste des campagnes",
        'peut_voir_detail_campagnes' => "Voir le détail des campagnes",

        // CONTACTS
        'peut_voir_contacts' => "Voir les contacts",
        'peut_gerer_contacts' => "Gérer les contacts",

        // UTILISATEURS
        'peut_creer_utilisateur' => "Créer des utilisateurs",
        'peut_modifier_utilisateur' => "Modifier des utilisateurs",
        'peut_supprimer_utilisateur' => "Supprimer des utilisateurs",
        'peut_attribuer_permissions' => "Attribuer des permissions",

        // CONTENU & SEGMENTATION
        'peut_segmenter_audience' => "Segmenter l'audience",
        'peut_personnaliser_contenu' => "Personnaliser le contenu",
        'peut_localiser_contenu' => "Adapter la langue",

        // BUDGET & CRÉDITS
        'peut_gerer_budget' => "Gérer le budget",
        'peut_definir_alertes_budget' => "Définir des alertes budget",
        'peut_voir_budget' => "Voir le budget",
        'peut_recharger_credits' => "Recharger des crédits",

        // ROUTAGE & PARAMÈTRES
        'peut_gerer_routage_sms' => "Gérer le routage SMS",
        'peut_configurer_parametres_globaux' => "Configurer les paramètres globaux",
        'peut_integrer_outils_externes' => "Intégrer des outils externes",

        // API & DEV
        'peut_acceder_api' => "Accéder à l'API",
        'peut_voir_analytiques_api' => "Voir les statistiques API",
        'peut_developper_plugins' => "Développer des plugins",

        // ALERTES & IMPACT
        'peut_envoyer_alertes_publiques' => "Envoyer des alertes publiques",
        'peut_voir_tableau_impact' => "Voir le tableau d'impact",
        'peut_collecter_retours' => "Collecter des retours",
        'peut_voir_reponses_clients' => "Voir les réponses clients",
        'peut_recevoir_notifications' => "Recevoir des notifications",

        // BRANDING
        'peut_ajouter_branding' => "Ajouter du branding",
    ];
}

}
