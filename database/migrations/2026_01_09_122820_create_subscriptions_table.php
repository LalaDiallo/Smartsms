<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->restrictOnDelete();

            $table->foreignId('billing_cycle_id')
                ->constrained('billing_cycles')
                ->restrictOnDelete();

            // Dates
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->dateTime('next_billing_date')->nullable();

            // Statut
            $table->enum('status', [
                'pending',
                'active',
                'suspended',
                'expired',
                'cancelled'
            ])->default('pending');

            // Facturation
            $table->boolean('auto_renew')->default(true);
            $table->decimal('price', 10, 2);
            $table->string('currency', 5)->default('GNF');

            // SMS
            $table->integer('sms_quota')->default(0);
            $table->integer('sms_used')->default(0);

            // Audit
            $table->timestamps();

            // Index utiles
            $table->index(['client_id', 'status']);
            $table->index('next_billing_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
