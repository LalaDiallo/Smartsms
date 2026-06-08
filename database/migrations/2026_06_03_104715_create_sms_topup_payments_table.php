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
        Schema::create('sms_topup_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->integer('sms_count');            // nombre de SMS à ajouter
            $table->decimal('price', 12, 2);          // montant payé
            $table->string('currency', 3)->default('GNF');
            $table->string('payment_ref')->nullable()->index(); // pay_id LengoPay
            $table->string('status', 20)->default('pending');   // pending|paid|failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_topup_payments');
    }
};
