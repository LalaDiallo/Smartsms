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
        Schema::table('sender_names', function (Blueprint $table) {
            // Métadonnées du formulaire : type_client, adresse, CNI/NIF, pièces jointes, engagements
            $table->json('metadata')->nullable()->after('document_path');
        });
    }

    public function down(): void
    {
        Schema::table('sender_names', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
