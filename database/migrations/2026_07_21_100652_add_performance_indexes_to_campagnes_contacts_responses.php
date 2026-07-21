<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->index(['client_id', 'zone_id'], 'campagnes_client_zone_idx');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index(['client_id', 'zone_id'], 'contacts_client_zone_idx');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->index('received_at', 'responses_received_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropIndex('campagnes_client_zone_idx');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_client_zone_idx');
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('responses_received_at_idx');
        });
    }
};
