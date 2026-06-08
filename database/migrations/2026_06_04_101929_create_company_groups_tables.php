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
        // ── Groupes d'entreprise ──────────────────────────────────────────────
        Schema::create('company_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('industry')->nullable();
            $table->unsignedBigInteger('owner_client_id')->index(); // client principal (admin groupe)
            $table->string('logo')->nullable();
            $table->string('status', 20)->default('active'); // active|suspended
            $table->timestamps();
        });

        // ── Branches (filiales) du groupe ─────────────────────────────────────
        Schema::create('company_group_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->index();
            $table->unsignedBigInteger('client_id')->index();     // client SmartSMS = la branche
            $table->string('zone_name');                           // ex: "Agence Conakry"
            $table->string('zone_type', 30)->default('region');   // region|city|sector
            $table->integer('sms_quota_allocated')->default(0);   // quota distribué à cette branche
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('company_groups')->onDelete('cascade');
            $table->unique(['group_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_group_branches');
        Schema::dropIfExists('company_groups');
    }
};
