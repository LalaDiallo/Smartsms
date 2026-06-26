<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PERMS = [
        // Gestion des campagnes de sa zone
        'peut_creer_campagne'                    => 1,
        'peut_gerer_campagne'                     => 1,
        'peut_envoyer_campagne'                   => 1,
        'peut_supprimer_campagne'                 => 1,
        'peut_modifier_details_mineurs_campagne'  => 1,
        'peut_approuver_campagne'                 => 1,
        // Gestion des contacts de sa zone
        'peut_gerer_contacts'                      => 1,
        // Gestion des membres locaux (operator/manager/developer) de sa zone
        'peut_creer_utilisateur'                   => 1,
        'peut_modifier_utilisateur'                => 1,
        'peut_supprimer_utilisateur'                => 1,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('roles_permissions')->where('role', 'responsable_regional')->exists();

        if ($exists) {
            DB::table('roles_permissions')->where('role', 'responsable_regional')->update(self::PERMS);
        } else {
            DB::table('roles_permissions')->insert(array_merge(['role' => 'responsable_regional'], self::PERMS));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles_permissions')
            ->where('role', 'responsable_regional')
            ->update(array_map(fn () => 0, self::PERMS));
    }
};
