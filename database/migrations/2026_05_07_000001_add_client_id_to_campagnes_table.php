<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            if (!Schema::hasColumn('campagnes', 'client_id')) {
                $table->unsignedBigInteger('client_id')->nullable()->after('user_id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            }
        });

        // Backfill client_id depuis les users existants
        DB::statement('
            UPDATE campagnes c
            JOIN users u ON c.user_id = u.id
            SET c.client_id = u.client_id
            WHERE c.client_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('campagnes', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
