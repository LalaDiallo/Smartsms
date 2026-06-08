<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('roles_permissions', 'peut_voir_contacts')) {
                $table->boolean('peut_voir_contacts')->default(false)->after('peut_voir_campagnes');
            }
            if (!Schema::hasColumn('roles_permissions', 'peut_gerer_contacts')) {
                $table->boolean('peut_gerer_contacts')->default(false)->after('peut_voir_contacts');
            }
            if (!Schema::hasColumn('roles_permissions', 'peut_supprimer_campagne')) {
                $table->boolean('peut_supprimer_campagne')->default(false)->after('peut_gerer_campagne');
            }
        });

        // Backfill : copier depuis les colonnes existantes
        DB::statement("
            UPDATE roles_permissions SET
                peut_voir_contacts  = peut_voir_campagnes,
                peut_gerer_contacts = peut_creer_utilisateur,
                peut_supprimer_campagne = peut_supprimer_utilisateur
        ");
    }

    public function down(): void
    {
        Schema::table('roles_permissions', function (Blueprint $table) {
            $table->dropColumn(['peut_voir_contacts', 'peut_gerer_contacts', 'peut_supprimer_campagne']);
        });
    }
};
