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
        Schema::table('company_group_branches', function (Blueprint $table) {
            $table->dropColumn('sms_quota_allocated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_group_branches', function (Blueprint $table) {
            $table->integer('sms_quota_allocated')->default(0);
        });
    }
};
