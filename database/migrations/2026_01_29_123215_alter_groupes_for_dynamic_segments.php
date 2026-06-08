<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            if (!Schema::hasColumn('groupes', 'type')) {
                $table->enum('type', ['static', 'dynamic'])
                      ->default('static')
                      ->after('name');
            }

            if (!Schema::hasColumn('groupes', 'description')) {
                $table->text('description')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            $table->dropColumn(['type', 'description']);
        });
    }
};
