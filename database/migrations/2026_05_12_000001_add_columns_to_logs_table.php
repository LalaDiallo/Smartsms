<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('user_id');
            $table->string('resource_type', 50)->nullable()->after('action');
            $table->unsignedBigInteger('resource_id')->nullable()->after('resource_type');
            $table->string('ip_address', 45)->nullable()->after('resource_id');

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');

            $table->index(['client_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('resource_type');
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropIndex(['client_id', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['resource_type']);
            $table->dropColumn(['client_id', 'resource_type', 'resource_id', 'ip_address']);
        });
    }
};
