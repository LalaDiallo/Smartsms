<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'timezone')) {
                $table->string('timezone')->nullable()->after('region');
            }

            if (!Schema::hasColumn('contacts', 'engagement_score')) {
                $table->integer('engagement_score')->default(0)->after('timezone');
            }

        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'engagement_score', 'preferred_channel']);
        });
    }
};
